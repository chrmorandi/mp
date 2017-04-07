<?php

namespace frontend\models;

use Yii;
use common\models\User;
use common\models\Sms;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\Reminder;
use frontend\models\UserContact;
use frontend\models\UserSetting;

/**
 * This is the model class for table "meeting_reminder".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $user_id
 * @property integer $reminder_id
 * @property integer $due_at
 * @property integer $status
 *
 * @property Meeting $meeting
 * @property User $user
 */
class MeetingReminder extends \yii\db\ActiveRecord
{
  const STATUS_PENDING = 0;
  const STATUS_COMPLETE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_reminder';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'user_id','reminder_id', 'due_at', 'status'], 'integer'],
            [['user_id', 'due_at'], 'required'],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
            [['reminder_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reminder::className(), 'targetAttribute' => ['reminder_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'reminder_id' => Yii::t('frontend', 'Reminder ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'due_at' => Yii::t('frontend', 'Due At'),
            'status' => Yii::t('frontend', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return MeetingReminderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MeetingReminderQuery(get_called_class());
    }

    // to do
    // - when user changes a reminder, reset all their meeting reminders for that reminder id
    // - when user deletes a reminder, delete all meetings reminders for that reminder id
    // - when user adds a reminder, create it for all meetings not yet complete
    // - when user edits a reminder, update it for all meetings not yet complete
    // clears and re-adds meeting reminders for a meeting for each user
    public static function reset($meeting_id,$user_id) {
        // delete all reminder for this meeting and user
         MeetingReminder::deleteAll(['meeting_id'=>$meeting_id,'user_id'=>$user_id]);
    }

    public static function create($meeting_id,$user_id,$reminder_id,$differential) {
        // delete any previously existing meetingreminder for this reminder_id and meeting_id
         MeetingReminder::deleteAll(['meeting_id'=>$meeting_id,'reminder_id'=>$reminder_id]);
         $mtg = Meeting::findOne($meeting_id);
         if (is_null($mtg)) {
           return false;
         }
         $chosen_time = Meeting::getChosenTime($meeting_id);
         $mr = new MeetingReminder;
         $mr->reminder_id = $reminder_id;
         $mr->meeting_id = $meeting_id;
         $mr->user_id = $user_id;
         $mr->due_at = $chosen_time->start-$differential;
         if ($mr->due_at>time()) {
           $mr->status=MeetingReminder::STATUS_PENDING;
         } else {
           $mr->status=MeetingReminder::STATUS_COMPLETE;
         }
         $mr->save();
    }

    // frequent cron task will call to check on due reminders
    public static function check() {
      $mrs = MeetingReminder::find()->where('due_at<='.time().' and status='.MeetingReminder::STATUS_PENDING)->all();
      foreach ($mrs as $mr) {
        // process each meeting reminder
        //var_dump($mr);continue;
        //var_dump($mr);echo '<p><br /></p>';
        MeetingReminder::process($mr);
      }
    }

    public static function process($mr) {
      // deliver the email or sms
      $user_id = $mr->user_id;
      $meeting_id = $mr->meeting_id;
      $mtg = Meeting::findOne($meeting_id);
      // fetch the reminder
      $reminder = Reminder::findOne($mr->reminder_id);
      // only send reminders for meetings that are confirmed
      if ($mtg->status!=Meeting::STATUS_CONFIRMED) return false;
      // only send reminders that are less than a day late - to do - remove after testing period
      if ((time()-$mr->due_at)>(24*3600+1)) return false;
      $u = \common\models\User::findOne($user_id);
      // ensure there is an auth key for the recipient user
      if (empty($u->auth_key)) {
        return false;
      }
      // prepare data for the message
      // get time
      $chosen_time = Meeting::getChosenTime($meeting_id);
      $timezone = MiscHelpers::fetchUserTimezone($user_id);
      $display_time = Meeting::friendlyDateFromTimestamp($chosen_time->start,$timezone);
      // build contact details for all other attendees
      $contacts_html = '';
      // get attendees
      $attendee_list = $mtg->buildAttendeeList();
      foreach ($attendee_list as $c) {
        if ($c['user_id']==$user_id) {
          // dont add user whose reminder this is
          continue;
        }
        $contacts_html .= UserContact::buildContactString($c['user_id'],'html');
      }
      $a=['user_id'=>$user_id,
       'auth_key'=>$u->auth_key,
       'email'=>$u->email,
       'username'=>$u->username
     ];
     // get place
     $chosen_place = Meeting::getChosenPlace($meeting_id);
     if ($chosen_place===false) {
       // virtual meeting
       $setViewMap = false;
     } else {
       $setViewMap = MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW_MAP,$chosen_place->place_id,$a['user_id'],$a['auth_key'],$mtg->site_id);
     }
     $isOwner = ($a['user_id']==$mtg->owner_id?true:false);
     $contactListObj = $mtg->getContactListObj($a['user_id'],$isOwner);
       // check if email is okay and okay from this sender_id
      if (User::checkEmailDelivery($user_id,0)) {
        $priorLanguage=\Yii::$app->language;
        $language = UserSetting::getLanguage($a['user_id']);
        if ($language!==false) {
          \Yii::$app->language=$language;
        }
          Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($user_id);
          // Build the absolute links to the meeting and commands
          $links=[
            'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            //'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'running_late'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_RUNNING_LATE,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'view_map'=>$setViewMap,
            'reminders'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_GO_REMINDERS,0,$a['user_id'],$a['auth_key'],$mtg->site_id)
          ];
          // send a text if sms or both
          if ($reminder->reminder_type!=Reminder::TYPE_EMAIL) {
            // look for verified phone #
            $to_number = UserContact::findUserNumber($user_id,UserContact::STATUS_VERIFIED);
            if ($to_number!==false) {
              // build text string and Url
              $sms_str= Yii::t('frontend','Reminder: Meeting coming up at ').$display_time.' '.$links['view'];
              Sms::transmit($to_number,$sms_str);
            }
          }
          // send an email if not exclusively sms
          if ($reminder->reminder_type!=Reminder::TYPE_SMS) {
            // send the message
            $message = Yii::$app->mailer->compose([
              'html' => 'reminder-html',
              'text' => 'reminder-text'
            ],
            [
              'meeting_id' => $mtg->id,
              'sender_id'=> $user_id,
              'user_id' => $a['user_id'],
              'auth_key' => $a['auth_key'],
              'display_time' => $display_time,
              'chosen_place' => $chosen_place,
              'contacts_html'=>$contacts_html, // to do - remove this now
              'contactListObj' => $contactListObj,
              'links' => $links,
              'showRunningLate'=>($chosen_time->start -time() <10800 )?true:false,
              'meetingSettings' => $mtg->meetingSettings,
          ]);
            if (!empty($a['email'])) {
              $message->setFrom(['support@meetingplanner.io'=>Yii::$app->params['site']['title']]);
              $message->setReplyTo('mp_'.$meeting_id.'@meetingplanner.io');
              $message->setTo($a['email'])
                  ->setSubject(Yii::t('frontend','Meeting Reminder: ').$mtg->subject)
                  ->send();
              }
              \Yii::$app->language=$priorLanguage;
          }
       }
      $mr->status=MeetingReminder::STATUS_COMPLETE;
      $mr->update();
    }
}
