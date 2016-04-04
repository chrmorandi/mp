<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;

/**
 * This is the model class for table "meeting".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $meeting_type
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
            [['owner_id', 'message'], 'required'],
            [['owner_id', 'meeting_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string']
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
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
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
         if ($canSend) {
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
         $margin=$tstamp-time();
         // less than a day ahead
         if ($margin<(24*3600)) {
           $date_str = Yii::$app->formatter->asDateTime($tstamp,'h:mm a');
         }   else {
           $date_str = Yii::$app->formatter->asDateTime($tstamp,'E MMM d,\' '.Yii::t('frontend','at').'\' h:mm a');         
         }
         return $date_str;
       }
      
}
