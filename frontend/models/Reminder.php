<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;
use common\models\User;
use frontend\models\MeetingReminder;

/**
 * This is the model class for table "reminder".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $duration_friendly
 * @property integer $unit
 * @property integer $duration
 * @property integer $reminder_type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Reminder extends \yii\db\ActiveRecord
{
  const UNIT_MINUTES = 0;
  const UNIT_HOURS = 10;
  const UNIT_DAYS = 20;

  const TYPE_EMAIL = 0;
  const TYPE_SMS = 10;
  const TYPE_BOTH = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'duration_friendly', 'unit', 'duration', 'reminder_type' ], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'duration_friendly' => Yii::t('frontend', 'Duration Friendly'),
            'unit' => Yii::t('frontend', 'Unit'),
            'duration' => Yii::t('frontend', 'Duration'),
            'reminder_type' => Yii::t('frontend', 'Reminder Type'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
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
     * @return ReminderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReminderQuery(get_called_class());
    }

    public static function initialize($user_id) {
      // check if any reminders exist
      $cnt=Reminder::find()->where(['user_id'=>$user_id])->count();
      if ($cnt>0) return false;
      // if not, create initial reminders for a user
      $r1 = new Reminder();
      $r1->user_id = $user_id;
      $r1->duration_friendly = 3;
      $r1->unit = Reminder::UNIT_HOURS;
      $r1->reminder_type = Reminder::TYPE_EMAIL;
      $r1->duration = (3600*3);
      $r1->validate();
      $r1->save();
      $r2 = new Reminder();
      $r2->user_id = $user_id;
      $r2->duration_friendly = 1;
      $r2->unit = Reminder::UNIT_DAYS;
      $r2->reminder_type = Reminder::TYPE_EMAIL;
      $r2->duration = 1*24*3600;
      $r2->save();
      $r3 = new Reminder();
      $r3->user_id = $user_id;
      $r3->duration_friendly = 3;
      $r3->unit = Reminder::UNIT_DAYS;
      $r3->reminder_type = Reminder::TYPE_EMAIL;
      $r3->duration = $r3->duration_friendly*24*3600;
      $r3->save();
      Reminder::processNewReminder($r1->id);
      Reminder::processNewReminder($r2->id);
      Reminder::processNewReminder($r3->id);
    }

    public static function displayUnits($unit) {
      switch ($unit) {
        case Reminder::UNIT_MINUTES:
          $str = 'minute(s)';
        break;
        case Reminder::UNIT_HOURS:
          $str = 'hour(s)';
        break;
        case Reminder::UNIT_DAYS:
          $str ='day(s)';
        break;
        default:
          $str ='unknown — please email support';
        break;
      }
      return $str;
    }

    public static function displayType($reminder_type) {
      switch ($reminder_type) {
        case Reminder::TYPE_EMAIL:
          $str = 'email';
        break;
        case Reminder::TYPE_BOTH:
          $str = 'email and text';
        break;
        case Reminder::TYPE_SMS:
          $str ='text';
        break;
        default:
          $str ='unknown — please email support';
        break;
      }
      return $str;
    }

    public static function setDuration($duration_friendly,$unit) {
      $cnt_sec = 0;
      switch ($unit) {
        case Reminder::UNIT_MINUTES:
          $cnt_sec = 60;
        break;
        case Reminder::UNIT_HOURS:
          $cnt_sec = 3600;
        break;
        case Reminder::UNIT_DAYS:
          $cnt_sec = 24*3600;
        break;
      }
      return $cnt_sec*$duration_friendly;
    }

    public static function processNewReminder($reminder_id) {
      // builds MeetingReminder row for all meetings attended by owner of this reminder
      $rem = Reminder::findOne($reminder_id);
      // find all the meetings this user is a part of
      // create meeting reminder for all meetings where this reminder's creator is the organizer
      $mtgs = Meeting::find()->where(['owner_id'=>$rem->user_id])->all();
      // to do performance - could add an open join above to participants
      foreach ($mtgs as $m) {
        MeetingReminder::create($m->id,$rem->user_id,$rem->id,$rem->duration);
      }
      // create meeting reminder for all meetings where this reminder's creator is a participant
      $part_mtgs = Participant::find()->where(['participant_id'=>$rem->user_id])->all();
      foreach ($part_mtgs as $p) {
        // fixed - there was a big bug here where I was using participant_id not p->meeting_id
        MeetingReminder::create($p->meeting_id,$rem->user_id,$rem->id,$rem->duration);
      }
    }

    public static function updateReminder($reminder_id) {
      // when user updates a reminder, update all the meeting reminders
      $new_reminder = Reminder::findOne($reminder_id);
      $mrs = MeetingReminder::find()->where(['reminder_id'=>$reminder_id])->all();
      // update each meeting reminder
      foreach ($mrs as $mr) {
        $chosen_time = Meeting::getChosenTime($mr->meeting_id);
        $mr->due_at = $chosen_time->start-$new_reminder->duration;
        if ($mr->due_at>time()) {
          $mr->status=MeetingReminder::STATUS_PENDING;
        } else {
          $mr->status=MeetingReminder::STATUS_COMPLETE;
        }
        $mr->update();
      }
    }

    public static function setMeetingReminders($meeting_id,$chosen_time=false) {
      // when a meeting is finalized, set reminders for the chosen time for all participants
      $mtg = Meeting::findOne($meeting_id);
      if ($chosen_time ===false) {
        $chosen_time = Meeting::getChosenTime($meeting_id);
      }
      // create attendees list for organizer and participants
      $attendees = array();
      $attendees[0]=$mtg->owner_id;
      $cnt =1;
      foreach ($mtg->participants as $p) {
        if ($p->status ==Participant::STATUS_DEFAULT) {
          $attendees[$cnt]=$p->participant_id;
          $cnt+=1;
        }
      }
      // for each attendee
      foreach ($attendees as $a) {
        // for their reminders
        $rems = Reminder::find()->where(['user_id'=>$a])->all();
        foreach ($rems as $rem) {
          // create a meeting reminder for that reminder at that time
            MeetingReminder::create($meeting_id,$a,$rem->id,$rem->duration);
        }
      }
    }

    public static function processTimeChange($meeting_id,$chosen_time) {
      // when a meeting time is set or changes, reset the reminders for all participants
      // $chosen_time = Meeting::getChosenTime($meeting_id);
      // clear out old meeting reminders for all users for this meeting
      MeetingReminder::deleteAll(['meeting_id'=>$meeting_id]);
      // set meeting reminders for all users for this meeting
      // note each user has different reminders
      Reminder::setMeetingReminders($meeting_id,$chosen_time);
    }

}
