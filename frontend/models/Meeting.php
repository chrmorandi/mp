<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;
use common\models\Yiigun;
use common\components\MiscHelpers;

/**
 * This is the model class for table "meeting".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $meeting_type
 * @property string $subject
 * @property string $message
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $owner
 * @property MeetingLog[] $meetingLogs
 * @property MeetingNote[] $meetingNotes
 * @property MeetingPlace[] $meetingPlaces
 * @property MeetingTime[] $meetingTimes
 * @property MeetingSetting[] $meetingSettings
 * @property Participant[] $participants
 */

class Meeting extends \yii\db\ActiveRecord
{
  const TYPE_OTHER = 0;
  const TYPE_COFFEE = 10;
  const TYPE_BREAKFAST = 20;
  const TYPE_LUNCH = 30;
  const TYPE_PHONE = 40;
  const TYPE_VIDEO = 50;
  const TYPE_HAPPYHOUR = 60;
  const TYPE_DINNER = 70;
  const TYPE_DRINKS = 80;
  const TYPE_BRUNCH = 90;
  const TYPE_OFFICE = 100;

  const STATUS_PLANNING =0;
  const STATUS_SENT = 20;
  const STATUS_CONFIRMED = 40;
  const STATUS_COMPLETED = 50;
  const STATUS_CANCELED = 60;

  const VIEWER_ORGANIZER = 0;
  const VIEWER_PARTICIPANT = 10;

  const COMMAND_HOME = 5;
  const COMMAND_VIEW = 10;
  const COMMAND_VIEW_MAP = 20;
  const COMMAND_FINALIZE = 50;
  const COMMAND_CANCEL = 60;
  const COMMAND_ACCEPT_ALL = 70;
  const COMMAND_ACCEPT_PLACE = 100;
  const COMMAND_REJECT_PLACE = 110;
  const COMMAND_ACCEPT_ALL_PLACES = 120;
  const COMMAND_CHOOSE_PLACE = 150;
  const COMMAND_ACCEPT_TIME = 200;
  const COMMAND_REJECT_TIME = 210;
  const COMMAND_ACCEPT_ALL_TIMES = 220;
  const COMMAND_CHOOSE_TIME = 250;
  const COMMAND_ADD_PLACE = 300;
  const COMMAND_ADD_TIME = 310;
  const COMMAND_ADD_NOTE = 320;
  const COMMAND_FOOTER_EMAIL = 400;
  const COMMAND_FOOTER_BLOCK = 410;
  const COMMAND_FOOTER_BLOCK_ALL = 420;

  public $title;
  public $viewer;
  public $viewer_id;
  public $isReadyToSend = false;
  public $isReadyToFinalize = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting';
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
            [['owner_id', 'subject'], 'required'],
            [['owner_id', 'meeting_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['message','subject'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'owner_id' => Yii::t('frontend', 'Owner ID'),
            'meeting_type' => Yii::t('frontend', 'Meeting Type'),
            'subject' => Yii::t('frontend', 'Subject'),
            'message' => Yii::t('frontend', 'Message'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function isOwner($viewer_id) {
      if ($viewer_id==$this->owner_id)
        return true;
      else
        return false;
    }

    public function initializeMeetingSetting($meeting_id,$owner_id) {
      // load meeting creator (owner) user settings to initialize meeting_settings
      UserSetting::initialize($owner_id); // if not initialized
      $user_setting = UserSetting::find()->where(['user_id' => $owner_id])->one();
      $meeting_setting = new MeetingSetting();
      $meeting_setting->meeting_id = $meeting_id;
      $meeting_setting->participant_add_place=$user_setting->participant_add_place;
      $meeting_setting->participant_add_date_time=$user_setting->participant_add_date_time;
      $meeting_setting->participant_choose_place=$user_setting->participant_choose_place;
    $meeting_setting->participant_choose_date_time=$user_setting->participant_choose_date_time;
      $meeting_setting->participant_finalize=$user_setting->participant_finalize;
      $meeting_setting->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'owner_id']);
    }

    public function setViewer() {
      $this->viewer_id = Yii::$app->user->getId();
      if ($this->owner_id == $this->viewer_id) {
        $this->viewer = Meeting::VIEWER_ORGANIZER;
      } else {
        $this->viewer = Meeting::VIEWER_PARTICIPANT;
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingSettings()
    {
        return $this->hasOne(MeetingSetting::className(), ['meeting_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingLogs()
    {
        return $this->hasMany(MeetingLog::className(), ['meeting_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingNotes()
    {
        return $this->hasMany(MeetingNote::className(), ['meeting_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingPlaces()
    {
        return $this->hasMany(MeetingPlace::className(), ['meeting_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingTimes()
    {
        return $this->hasMany(MeetingTime::className(), ['meeting_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParticipants()
    {
        return $this->hasMany(Participant::className(), ['meeting_id' => 'id']);
    }

    public function getMeetingType($data) {
      $options = $this->getMeetingTypeOptions();
	  if (!isset($options[$data])) {
		$data = self::TYPE_OTHER;
		}
      return $options[$data];
    }

    public function getMeetingTypeOptions()
    {
      return array(
        self::TYPE_OFFICE => 'Office',
        self::TYPE_COFFEE => 'Coffee',
        self::TYPE_BREAKFAST => 'Breakfast',
        self::TYPE_LUNCH => 'Lunch',
        self::TYPE_PHONE => 'Phone call',
        self::TYPE_VIDEO => 'Video conference',
        self::TYPE_HAPPYHOUR => 'Happy hour',
        self::TYPE_DINNER => 'Dinner',
        self::TYPE_DRINKS => 'Drinks',
        self::TYPE_BRUNCH => 'Brunch',
        self::TYPE_OTHER => 'Other',
         );
     }

     public function getMeetingHeader() {
       $str = $this->getMeetingType($this->meeting_type);
       if ($this->isOwner(Yii::$app->user->getId())) {
         if (count($this->participants)>0) {
           $str.=Yii::t('frontend',' with ');
           $str.=$this->participants[0]->participant->email;
         }
       } else {
         $owner = \common\models\User::findIdentity($this->owner_id);
         $str.=Yii::t('frontend',' with ');
         $str.=$owner->email;
       }
       return $str;
     }

     public function getMeetingTitle($meeting_id) {
        $meeting = Meeting::find()->where(['id' => $meeting_id])->one();
        $title = $this->getMeetingType($meeting->meeting_type);
        $title.=' Meeting';
        return $title;
     }

     public function reschedule($meeting_id) {

     }

     public function canSend($sender_id) {
       // check if an invite can be sent
       // req: a participant, at least one place, at least one time
       if ($this->owner_id == $sender_id
        && count($this->participants)>0
        && count($this->meetingPlaces)>0
        && count($this->meetingTimes)>0
        ) {
         $this->isReadyToSend = true;
       } else {
         $this->isReadyToSend = false;
       }
       return $this->isReadyToSend;
      }

      public function canFinalize($user_id) {
        $this->isReadyToFinalize = false;
        // check if meeting can be finalized by viewer
        // check if overall meeting state can be sent by owner
         if (!$this->canSend($this->owner_id)) return false;
          $chosenPlace = false;
          if (count($this->meetingPlaces)==1) {
            $chosenPlace = true;
          } else {
            foreach ($this->meetingPlaces as $mp) {
              if ($mp->status == MeetingPlace::STATUS_SELECTED) {
                $chosenPlace = true;
                break;
              }
            }
          }
          $chosenTime = false;
          if (count($this->meetingTimes)==1) {
            $chosenTime = true;
          } else {
            foreach ($this->meetingTimes as $mt) {
              if ($mt->status == MeetingTime::STATUS_SELECTED) {
                  $chosenTime = true;
                  break;
              }
            }
          }
          if ($this->owner_id == $user_id ||
          $this->meetingSettings->participant_finalize) {
            if ($chosenPlace && $chosenTime) {
              $this->isReadyToFinalize = true;
            }
          }
        return $this->isReadyToFinalize;
      }

  public function send($user_id) {
    // $user_id is the owner of the meeting
    // has the meeting already been sent
    if ($this->status != Meeting::STATUS_PLANNING) return false;
    $notes=MeetingNote::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_DESC])->limit(3)->all();
    $places = MeetingPlace::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_ASC])->all();
    $times = MeetingTime::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_ASC])->all();
    // Get message header
    $header = $this->getMeetingHeader();
  // to do - loop through participants
  foreach ($this->participants as $p) {
    // Build the absolute links to the meeting and commands
    $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
    $links=[
      'home'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_HOME,0,$p->participant_id,$auth_key),
      'view'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_VIEW,0,$p->participant_id,$auth_key),
      'finalize'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FINALIZE,0,$p->participant_id,$auth_key),
      'cancel'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_CANCEL,0,$p->participant_id,$auth_key),
      'acceptall'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL,0,$p->participant_id,$auth_key),
      'acceptplaces'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_PLACES,0,$p->participant_id,$auth_key),
      'accepttimes'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_TIMES,0,$p->participant_id,$auth_key),
      'addplace'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_PLACE,0,$p->participant_id,$auth_key),
      'addtime'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_TIME,0,$p->participant_id,$auth_key),
      'addnote'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_NOTE,0,$p->participant_id,$auth_key),
      'footer_email'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_EMAIL,0,$p->participant_id,$auth_key),
      'footer_block'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK,0,$p->participant_id,$auth_key),
      'footer_block_all'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$p->participant_id,$auth_key),
    ];
    // send the message
    $message = Yii::$app->mailer->compose([
      'html' => 'invitation-html',
      'text' => 'invitation-text'
    ],
    [
      'meeting_id' => $this->id,
      'participant_id' => 0,
      'owner' => $this->owner->username,
      'user_id' => $p->participant_id,
      'auth_key' => $auth_key,
      'intro' => $this->message,
      'links' => $links,
      'header' => $header,
      'places' => $places,
      'times' => $times,
      'notes' => $notes,
      'meetingSettings' => $this->meetingSettings,
  ]);
    // to do - add full name
          $message->setFrom(array('support@meetingplanner.com'=>$this->owner->email));
          $message->setTo($p->participant->email)
              ->setSubject('Meeting Request: '.$this->subject)
              ->send();
  }
  // send the meeting
  $this->status = Meeting::STATUS_SENT;
  // to do - uncomment this
  //$this->update();
  }

      public function finalize($user_id) {

      }

      public function cancel() {
        $this->status = self::STATUS_CANCELED;
        $this->save();
      }

      public function prepareView() {
        $this->setViewer();
        $canSend = $this->canSend($this->viewer_id);
        $this->canFinalize($this->viewer_id);
        // has invitation been sent
         if ($canSend && $this->status < Meeting::STATUS_SENT) {
           Yii::$app->session->setFlash('warning', Yii::t('frontend','This invitation has not yet been sent.'));
      }
        // to do - if sent, has invitation been opened
        // to do - if not finalized, is it within 72 hrs, 48 hrs
      }

      public static function friendlyDateFromTimeString($time_str) {
        $tstamp = strtotime($time_str);
        return $this->friendlyDateFromTimeString($tstamp);
      }

       // formatting helpers
       public static function friendlyDateFromTimestamp($tstamp) {
         // same day as today?
         if (date('z')==date('z',$tstamp)) {
           $date_str = Yii::t('frontend','Today at ').Yii::$app->formatter->asDateTime($tstamp,'h:mm a');
         }   else {
           $date_str = Yii::$app->formatter->asDateTime($tstamp,'E MMM d,\' '.Yii::t('frontend','at').'\' h:mm a');
         }
         return $date_str;
       }

       public function afterSave($insert,$changedAttributes)
       {
           parent::afterSave($insert,$changedAttributes);
           if ($insert) {
             // if Meeting is added
             MeetingLog::add($this->id,MeetingLog::ACTION_CREATE_MEETING,$this->owner_id);
           }
       }
}
