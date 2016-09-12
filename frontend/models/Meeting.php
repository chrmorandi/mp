<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;
use common\models\Yiigun;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\UserContact;
use frontend\models\Participant;
use frontend\models\MeetingLog;

//use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "meeting".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property integer $meeting_type
 * @property string $subject
 * @property string $message
 * @property string $identifier
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property integer $sequence_id
 * @property integer $cleared_at
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
  const TYPE_NEW = 0;
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
  const TYPE_OTHER = 110;
  const TYPE_VIRTUAL = 150;

  const STATUS_PLANNING =0;
  const STATUS_SENT = 20;
  const STATUS_CONFIRMED = 40; // finalized
  const STATUS_COMPLETED = 50;
  const STATUS_EXPIRED = 55;
  const STATUS_CANCELED = 60;
  const STATUS_TRASH = 70;

  const VIEWER_ORGANIZER = 0;
  const VIEWER_PARTICIPANT = 10;

  const COMMAND_HOME = 5;
  const COMMAND_VIEW = 10;
  const COMMAND_VIEW_MAP = 20;
  const COMMAND_VIEW_REQUEST = 25;
  const COMMAND_FINALIZE = 50;
  const COMMAND_CANCEL = 60;
  const COMMAND_DECLINE = 65;
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
  const COMMAND_ADD_CONTACT = 330;
  const COMMAND_RUNNING_LATE = 350;
  const COMMAND_FOOTER_EMAIL = 400;
  const COMMAND_FOOTER_BLOCK = 410;
  const COMMAND_FOOTER_BLOCK_ALL = 420;
  const COMMAND_NO_UPDATES = 425;
  const COMMAND_NO_NEWSLETTER = 430;
  const COMMAND_GO_REMINDERS = 450;
  const COMMAND_VERIFY_EMAIL = 460;
  const COMMAND_RESPOND_MESSAGE = 470;
  const COMMAND_DOWNLOAD_ICS = 480;

  const ABANDONED_AGE = 3; // weeks

  const SWITCH_INPERSON =1;
  const SWITCH_VIRTUAL =0;

  const NEAR_LIMIT = 7;
  const DAY_LIMIT = 12;
  const RESEND_LIMIT = 3;

  const DEFAULT_NEW_MEETING = 'New Meeting';
  const DEFAULT_SUBJECT = 'Our Upcoming Meeting';

  public $has_subject = false;
  public $title;
  public $note;
  public $viewer;
  public $viewer_id;
  public $isReadyToSend = false;
  public $isReadyToFinalize = false;
  public $dataCount;
  public $switchVirtual = Meeting::SWITCH_INPERSON;

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
            /*[
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'immutable' => true,
                'ensureUnique'=>true,
            ],*/
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
            [['owner_id'], 'required'],
            [['owner_id', 'meeting_type', 'status', 'created_at', 'updated_at','sequence_id'], 'integer'],
            [['message','subject','identifier'], 'string']
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
            'identifier' => Yii::t('frontend', 'Identifier'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function isOrganizer() {
      $user_id = Yii::$app->user->getId();
      if ($user_id == $this->owner_id) {
        return true;
      } else {
        foreach ($this->participants as $p) {
          if ($user_id == $p->participant_id) {
            if ($p->participant_type == Participant::TYPE_ORGANIZER) {
              return true;
            } else {
              return false;
            }
          }
      }
    }
    return false;
  }


    public function isOwner($user_id) {
      if ($user_id==$this->owner_id) {
        return true;
      } else {
        return false;
      }
    }

    public function isGroup() {
      if (count($this->participants)>=2) {
        return true;
      } else {
        return false;
      }
    }

    public function initializeMeetingSetting($meeting_id,$owner_id) {
      $checkMtgStg = MeetingSetting::find()->where(['meeting_id' => $meeting_id])->one();
      if (is_null($checkMtgStg)) {
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
        $meeting_setting->participant_reopen=$user_setting->participant_reopen;
        $meeting_setting->participant_request_change=$user_setting->participant_request_change;
        $meeting_setting->save();
      }
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
		$data = self::TYPE_NEW;
		}
      return $options[$data];
    }

    public function getMeetingTypeOptions()
    {
      return array(
        self::TYPE_NEW => 'New meeting',
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
        self::TYPE_VIRTUAL => 'Virtual',
         );
     }

     public function getMeetingTitle($meeting_id) {
        $m = Meeting::find()->where(['id' => $meeting_id])->one();
        if (empty($m->subject)) {
          $str = Yii::t('frontend',Meeting::DEFAULT_SUBJECT);
        } else {
          $str = $m->subject;
        }
        return $str;
     }

     public function getMeetingHeader($source='index') {
       // returns a subject to display
       if (empty($this->subject)) {
         $str = Yii::t('frontend',Meeting::DEFAULT_SUBJECT); // 'Our Upcoming Meeting'
         $this->has_subject = false;
       } else {
         $this->has_subject = true;
         $str = $this->subject;
       }
       return $str;
     }

     public function getMeetingParticipants($user_id=0, $prefix = true, $exclude_owner = false) {
       // get a string of the participants other than the viewer
       if ($user_id == 0) {
         $user_id = Yii::$app->user->getId();
       }
       $str='';
       $listPeople=[];
       if (!$exclude_owner && !$this->isOwner($user_id)) {
         $listPeople[]=$this->owner_id;
       }
       if (count($this->participants)>0) {
         foreach ($this->participants as $p) {
           if ($p->status != Participant::STATUS_DEFAULT) {
             // skip those that have canceled or been removed
             continue;
           }
           if ($p->participant->id<>$user_id) {
             $listPeople[]=$p->participant->id;
           }
        }
        $str.=MiscHelpers::listNames($listPeople);
        return (($prefix && strlen($str)>0)?'with '.$str:$str);
       }
     }

     public function canSend($sender_id) {
       // check if an invite can be sent
       // req: a participant, at least one place, at least one time
       $cntPlaces = 0;
       foreach($this->meetingPlaces as $mp) {
         if ($mp->status!=MeetingPlace::STATUS_REMOVED) {
           $cntPlaces+=1;
         }
       }
       $cntTimes = 0;
       foreach($this->meetingTimes as $mt) {
         if ($mt->status!=MeetingTime::STATUS_REMOVED) {
           $cntTimes+=1;
         }
       }
       if ($this->owner_id == $sender_id
        && count($this->participants)>0
        && ($cntPlaces>0 || $this->isVirtual())
        && $cntTimes>0
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
        $chosenPlace = false;
        $chosenTime = false;
        $cntPlaces = 0;
        foreach($this->meetingPlaces as $mp) {
          if ($mp->status!=MeetingPlace::STATUS_REMOVED) {
            $cntPlaces+=1;
            if ($mp->status == MeetingPlace::STATUS_SELECTED) {
              $chosenPlace = true;
            }
          }
        }
        $cntTimes = 0;
        foreach($this->meetingTimes as $mt) {
          if ($mt->status!=MeetingTime::STATUS_REMOVED) {
            $cntTimes+=1;
            if ($mt->status == MeetingTime::STATUS_SELECTED) {
                $chosenTime = true;
            }
          }
        }
         if (!$this->canSend($this->owner_id)) return false;
          if ($cntPlaces==1 || $this->isVirtual()) {
            $chosenPlace = true;
          }
          if ($cntTimes==1) {
            $chosenTime = true;
          }
          if ($this->owner_id == $user_id ||
          $this->meetingSettings->participant_finalize) {
            if ($chosenPlace && $chosenTime && $this->isSomeoneAvailable()) {
              $this->isReadyToFinalize = true;
            }
          }
        return $this->isReadyToFinalize;
      }

  public function send($user_id,$isResend = false) {
    // $user_id is the owner of the meeting
    // has the meeting already been sent
    if (!$isResend && $this->status != Meeting::STATUS_PLANNING) return false;
    $notes=MeetingNote::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_DESC])->limit(3)->all();
    $places = MeetingPlace::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_ASC])->all();
    $times = MeetingTime::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_ASC])->all();
    // Get message header
    $header = $this->getMeetingHeader();
  foreach ($this->participants as $p) {
    if ($p->status !=Participant::STATUS_DEFAULT) {
      continue;
    }
    // Build the absolute links to the meeting and commands
    $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
    $links=[
      'home'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_HOME,0,$p->participant_id,$auth_key),
      'view'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_VIEW,0,$p->participant_id,$auth_key),
      'finalize'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FINALIZE,0,$p->participant_id,$auth_key),
      'cancel'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_CANCEL,0,$p->participant_id,$auth_key),
      'decline'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_DECLINE,0,$p->participant_id,$auth_key),
      'acceptall'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL,0,$p->participant_id,$auth_key),
      'acceptplaces'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_PLACES,0,$p->participant_id,$auth_key),
      'accepttimes'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_TIMES,0,$p->participant_id,$auth_key),
      'addplace'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_PLACE,0,$p->participant_id,$auth_key),
      'addtime'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_TIME,0,$p->participant_id,$auth_key),
      'addnote'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_NOTE,0,$p->participant_id,$auth_key),
      'footer_email'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_EMAIL,0,$p->participant_id,$auth_key),
      'footer_block'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK,$this->owner_id,$p->participant_id,$auth_key),
      'footer_block_all'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$p->participant_id,$auth_key),
    ];
    $participantList = $this->getMeetingParticipants($p->participant_id,true,true);
    if ($this->isVirtual()) {
      $noPlaces = true;
    } else {
      $noPlaces = false;
    }
    // send the message
    // check if email is okay and okay from this sender_id
    if (User::checkEmailDelivery($p->participant_id,$this->owner_id)) {
      Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($p->participant_id);
      $message = Yii::$app->mailer->compose([
        'html' => 'invitation-html',
        'text' => 'invitation-text'
      ],
      [
        'meeting_id' => $this->id,
        'noPlaces' => $noPlaces,
        'participant_id' => 0,
        'participantList'=>$participantList,
        'owner' => MiscHelpers::getDisplayName($this->owner_id),
        'sender_id'=> $this->owner_id,
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
      $message->setFrom(array('support@meetingplanner.io'=>$this->owner->email));
      $message->setReplyTo('mp_'.$this->id.'@meetingplanner.io');
      $message->setTo($p->participant->email)
          ->setSubject(Yii::t('frontend','Meeting Request: ').$this->subject)
          ->send();
          // send the meeting
          $this->status = Meeting::STATUS_SENT;
          $this->cleared_at = time()+30;
          $this->update();
          // add to log
          MeetingLog::add($this->id,MeetingLog::ACTION_SEND_INVITE,$user_id,$p->participant_id);
        } else {
          // to do - post an error that user doesn't accept email or blocked them
        }
    }
  }

    public function finalize($user_id,$isResend=false) {
      // Get message header
      $header = $this->getMeetingHeader('confirmed');
      /*if ($this->subject == Meeting::DEFAULT_NEW_MEETING) {
        $this->subject = Meeting::DEFAULT_SUBJECT;
        $this->has_subject = true;
        $this->update();
      }*/
      if ($isResend) {
        $reopened = false;
        $finalPrefix = Yii::t('frontend','Meeting Renotification: ');
      } else {
        if (MeetingLog::countAction($this->id,MeetingLog::ACTION_FINALIZE_INVITE)>0) {
          $reopened = true;
          $finalPrefix = Yii::t('frontend','Meeting Modified: ');
          $this->increaseSequence();
        } else {
          $reopened = false;
          $finalPrefix = Yii::t('frontend','Meeting Confirmed: ');
        }
      }
      // to do - not all those links are needed in the view of a finalized meeting
      $notes=MeetingNote::find()->where(['meeting_id' => $this->id])->orderBy(['id' => SORT_DESC])->limit(3)->all();
      // chosen place
      if ($this->isVirtual()) {
        $noPlaces = true;
        $chosenPlace=false;
      } else {
        $noPlaces = false;
        $chosenPlace = $this->getChosenPlace($this->id);
      }
      // chosen time
      $chosenTime=$this->getChosenTime($this->id);
      // build an attendees array of both the organizer and the participants
      // to do - this can be replaced by buildAttendeeList
      // but friendship reciprocate needs to be reviewed and included
      $cnt =0;
      $attendees = array();
      foreach ($this->participants as $p) {
        if ($p->status !=Participant::STATUS_DEFAULT) {
          continue;
        }
        $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
        $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
        'email'=>$p->participant->email,
        'username'=>$p->participant->username];
        $cnt+=1;
        // reciprocate friendship to organizer
        \frontend\models\Friend::add($p->participant_id,$p->invited_by);
        // to do - reciprocate friendship in multi participant meetings
      }
      $auth_key=\common\models\User::find()->where(['id'=>$this->owner_id])->one()->auth_key;
      $attendees[$cnt]=['user_id'=>$this->owner_id,
        'auth_key'=>$auth_key,
        'email'=>$this->owner->email,
        'username'=>$this->owner->username];
    // use this code to send
    foreach ($attendees as $cnt=>$a) {
      // check if email is okay and okay from this sender_id
      if (User::checkEmailDelivery($a['user_id'],$user_id)) {
        $participantList = $this->getMeetingParticipants($a['user_id'],true,true);
        Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($a['user_id']);
        // Build the absolute links to the meeting and commands
        $links=[
          'home'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
          'view'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key']),
          'download'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_DOWNLOAD_ICS,0,$a['user_id'],$a['auth_key']),
          'finalize'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FINALIZE,0,$a['user_id'],$a['auth_key']),
          'cancel'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_CANCEL,0,$a['user_id'],$a['auth_key']),
          'acceptall'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL,0,$a['user_id'],$a['auth_key']),
          'acceptplaces'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_PLACES,0,$a['user_id'],$a['auth_key']),
          'accepttimes'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ACCEPT_ALL_TIMES,0,$a['user_id'],$a['auth_key']),
          'addplace'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_PLACE,0,$a['user_id'],$a['auth_key']),
          'addtime'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_TIME,0,$a['user_id'],$a['auth_key']),
          'addnote'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_ADD_NOTE,0,$a['user_id'],$a['auth_key']),
          'footer_email'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
          'footer_block'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK,$user_id,$a['user_id'],$a['auth_key']),
          'footer_block_all'=>MiscHelpers::buildCommand($this->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
        ];
        // send the message
        $message = Yii::$app->mailer->compose([
          'html' => 'finalize-html',
          'text' => 'finalize-text'
        ],
        [
          'meeting_id' => $this->id,
          'noPlaces' => $noPlaces,
          'participant_id' => 0,
          'participantList'=>$participantList,
          'owner' => MiscHelpers::getDisplayName($this->owner_id),
          'sender_id'=> $user_id,
          'user_id' => $a['user_id'],
          'auth_key' => $a['auth_key'],
          'intro' => $this->message,
          'links' => $links,
          'header' => $header,
          'chosenPlace' => $chosenPlace,
          'chosenTime' => $chosenTime,
          'notes' => $notes,
          'meetingSettings' => $this->meetingSettings,
          'reopened' => $reopened,
      ]);
        // to do - add full name
      $icsPath = Meeting::buildCalendar($this->id,$chosenPlace,$chosenTime,$a,$attendees);
      $message->setFrom(array('support@meetingplanner.io'=>$this->owner->email));
      $message->setReplyTo('mp_'.$this->id.'@meetingplanner.io');
      $message->attachContent(file_get_contents($icsPath), ['fileName' => 'meeting.ics', 'contentType' => 'text/calendar']);
      $message->setTo($a['email'])
          ->setSubject($finalPrefix.$this->subject)
          ->send();
      }
          $this->status = self::STATUS_CONFIRMED;
          $this->update();
      }
      // create all of each users reminders
      Reminder::processTimeChange($this->id,$chosenTime);
      // add to log
      if (!$isResend) {
        MeetingLog::add($this->id,MeetingLog::ACTION_FINALIZE_INVITE,$user_id,0);
        Meeting::clearLog($this->id);
      }
      if ($this->isVirtual()) {
        Meeting::checkContactInformation($this->id);
      }
    }

      public function cancel($user_id) {
        if ($user_id == Yii::$app->user->getId() && $user_id == $this->owner_id) {
          $this->status = self::STATUS_CANCELED;
          $this->update();
          $this->increaseSequence();
          MeetingLog::add($this->id,MeetingLog::ACTION_CANCEL_MEETING,$user_id);
          $subject = $this->getMeetingHeader('cancelation');
          $p1 = MiscHelpers::getDisplayName($user_id).Yii::t('frontend',' canceled the meeting: ').$subject;
          $content=[
            'subject' => Yii::t('frontend','Your Meeting Has Been Canceled'),
            'heading' => Yii::t('frontend','Meeting Canceled'),
            'p1' => $p1,
            'p2' => '',
            'plain_text' => $p1.'...'.Yii::t('frontend','View the meeting here: '),
          ];
          $button= [
            'text' => Yii::t('frontend','View the Meeting'),
            'command' => Meeting::COMMAND_VIEW,
            'obj_id' => 0,
          ];
          $this->generic_notify($user_id,$this->id, $content,$button,true);
          return true;
        } else {
          return false;
        }
      }

      public function trash($user_id) {
        // to do - check if user can Delete
        if ($this->owner_id == $user_id && $this->status == self::STATUS_PLANNING) {
          $this->status = self::STATUS_TRASH;
          $this->update();
          $this->increaseSequence();
          MeetingLog::add($this->id,MeetingLog::ACTION_DELETE_MEETING,$user_id);
          return true;
        } else {
          return false;
        }
      }

      public function decline($user_id) {
        // user is declining participation
        // get participant_id and set status
        foreach ($this->participants as $p) {
          if ($p->participant_id == $user_id) {
            $p->status = Participant::STATUS_DECLINED;
            // to do - clean up, necessary because participant accepts public email right now and requires address
            $p->email = User::findOne($user_id)->email;
            $p->update();
            break;
          }
        }
        MeetingLog::add($this->id,MeetingLog::ACTION_DECLINE_MEETING,$user_id);
        // if meeting is confirmed and only person withdraws, then cancel it
        if (count($this->participants)==1 && $this->status == Meeting::STATUS_CONFIRMED) {
          // if there's only one participant, cancel the meeting
            $this->cancel($user_id);
        }
        return true;
      }

      public function prepareView() {
        $this->setViewer();
        // check for meeting_settings
        $this->initializeMeetingSetting($this->id,$this->owner_id);
        if ($this->isVirtual()) {
          $this->switchVirtual = Meeting::SWITCH_VIRTUAL;
          //echo 'here';exit;
        } else {
          $this->switchVirtual = Meeting::SWITCH_INPERSON;
          //echo 'here- live';exit;
        }
        $canSend = $this->canSend($this->viewer_id);
        $this->canFinalize($this->viewer_id);
        // has invitation been sent
         if ($canSend && $this->status < Meeting::STATUS_SENT) {
           Yii::$app->session->setFlash('warning', Yii::t('frontend','This invitation has not yet been sent.'));
         }
        // to do - if sent, has invitation been opened
        // to do - if not finalized, is it within 72 hrs, 48 hrs
      }

      public function beforeSave($insert)
      {
          if (parent::beforeSave($insert)) {
            if ($insert) {
              $this->identifier = Yii::$app->security->generateRandomString(8);
            }
          }
          return true;
      }

       public function afterSave($insert,$changedAttributes)
       {
           parent::afterSave($insert,$changedAttributes);
           if ($insert) {
             // if Meeting is added
             MeetingLog::add($this->id,MeetingLog::ACTION_CREATE_MEETING,$this->owner_id);
           }
       }

       public function buildAttendeeList() {
         // build an attendees array of both the organizer and the participants
         $cnt =0;
         $attendees = array();
         foreach ($this->participants as $p) {
           if ($p->status ==Participant::STATUS_DEFAULT) {
             $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
             $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
             'email'=>$p->participant->email,
             'username'=>$p->participant->username];
             $cnt+=1;
           }
         }
         $auth_key=\common\models\User::find()->where(['id'=>$this->owner_id])->one()->auth_key;
         $attendees[$cnt]=['user_id'=>$this->owner_id,'auth_key'=>$auth_key,
           'email'=>$this->owner->email,
           'username'=>$this->owner->username];
        return $attendees;
       }

           public function reopen() {
             // when organizer or participant with permission asks to make changes
             if (MeetingLog::withinActionLimit($this->id,MeetingLog::ACTION_REOPEN,Yii::$app->user->getId(),7)) {
               $this->status = Meeting::STATUS_SENT;
               $this->update();
               $this->increaseSequence();
               MeetingLog::add($this->id,MeetingLog::ACTION_REOPEN,Yii::$app->user->getId());
               return true;
             } else {
               // over limit per meeting
               return false;
             }
           }

           public function reschedule() {
             $newOwner = $user_id = Yii::$app->user->getId();
             // user can only cancel their own Meeting
             if ($this->owner_id == $user_id) {
               $addParticipant = false;
               $this->cancel($user_id);
               MeetingLog::add($this->id,MeetingLog::ACTION_RESCHEDULE,$user_id);
             } else {
                 // if user is participant - needs to reverse
                 if (!isAttendee($this->id,$user_id)) {
                   // user isn't owner or participant - error
                   return false;
                 } else {
                   // reverse the owner and participant
                   $addParticipant = $this->owner_id;
                 }
             }
             // create new meeting - as copy of old meeting
             $m = new Meeting();
             $m->attributes = $this->attributes;
             $m->owner_id = $newOwner;
             $m->status = Meeting::STATUS_PLANNING;
             $m->created_at = time();
             $m->updated_at = time();
             $m->logged_at = 0;
             $m->cleared_at = 0;
             $m->sequence_id = 0;
             $m->save();
             // clone the selected place (not all of them)
             $chosenPlace = $this->getChosenPlace($this->id);
             if ($chosenPlace!==false) {
               $mp = new MeetingPlace;
               $mp->suggested_by = $newOwner;
               $mp->attributes = $chosenPlace->attributes;
               $mp->meeting_id = $m->id;
               $mp->created_at = time();
               $mp->updated_at = time();
               $mp->save();
             }
             // clone the participants
             foreach ($this->participants as $p) {
               // skip if reschedule new owner was a participant
               if ($p->participant_id==$user_id) {
                 continue;
               }
               // note Participant afterSave will create choices for place
               $clone_p = new Participant();
               $clone_p->attributes = $p->attributes;
               $clone_p->email = User::findOne($p->participant_id)->email;
               $clone_p->meeting_id = $m->id;
               $clone_p->invited_by = $newOwner;
               $clone_p->status = Participant::STATUS_DEFAULT;
               $clone_p->created_at = time();
               $clone_p->updated_at = time();
               $clone_p->save();
             }
             // if participant asked to reschedule - not yet allowed
             // to do - review this code and finish this feature
             if ($addParticipant!==false) {
               $newP = new Participant();
               $newP->email = User::findOne($addParticipant)->email;
               $newP->meeting_id = $m->id;
               $newP->participant_id = $addParticipant;
               $newP->invited_by = $user_id;
               $newP->status = Participant::STATUS_DEFAULT;
               $newP->save();
             }
             return $m->id;
           }

           public function repeat() {
             // to do - expand repeat meeting to have more options
             // e.g. pick same dow and time in future week or two
             // e.g. duplicate chosenplace or all places
             // e.g. duplicate all participants or just some (complicated if particpant duplicates)
             $newOwner = $user_id = Yii::$app->user->getId();
             // if user is participant - needs to reverse
             if ($this->owner_id == $user_id) {
               $addParticipant = false;
             } else {
               if (!isAttendee($this->id,$user_id)) {
                 // user isn't owner or participant - error
                 return false;
               } else {
                 // reverse the owner and participant
                 $addParticipant = $this->owner_id;
               }
             }
             // create new meeting - as copy of old meeting
             $m = new Meeting();
             $m->attributes = $this->attributes;
             $m->owner_id = $newOwner;
             $m->status = Meeting::STATUS_PLANNING;
             $m->created_at = time();
             $m->updated_at = time();
             $m->logged_at = 0;
             $m->cleared_at = 0;
             $m->sequence_id = 0;
             $m->save();
             // get prior meetings selected time and create two future times for the next two weeks
             $chosenTime=$this->getChosenTime($this->id);
             $mt1 = MeetingTime::createTimePlus($m->id,$m->owner_id,$chosenTime->start,$chosenTime->duration);
             $mt2 = MeetingTime::createTimePlus($m->id,$m->owner_id,$mt1->start,$chosenTime->duration);
             // clone the selected place (not all of them)
             $chosenPlace = $this->getChosenPlace($this->id);
             if ($chosenPlace!==false) {
               $mp = new MeetingPlace;
               $mp->suggested_by = $newOwner;
               $mp->attributes = $chosenPlace->attributes;
               $mp->meeting_id = $m->id;
               $mp->created_at = time();
               $mp->updated_at = time();
               $mp->save();
             }
             // clone the participants
             foreach ($this->participants as $p) {
               // skip if reschedule new owner was a participant
               if ($p->participant_id==$user_id) {
                 continue;
               }
               // note Participant afterSave will create choices for place
               $clone_p = new Participant();
               $clone_p->attributes = $p->attributes;
               $clone_p->email = User::findOne($p->participant_id)->email;
               $clone_p->meeting_id = $m->id;
               $clone_p->status = Participant::STATUS_DEFAULT;
               $clone_p->created_at = time();
               $clone_p->updated_at = time();
               $clone_p->save();
             }
             // if participant asked to repeat
             // add the prior owner as a participant
             if ($addParticipant!==false) {
               $newP = new Participant();
               $newP->meeting_id = $m->id;
               $newP->participant_id = $addParticipant;
               $newP->invited_by = $user_id;
               $newP->status = Participant::STATUS_DEFAULT;
               $newP->created_at = time();
               $newP->updated_at = time();
               $newP->save();
             }
             MeetingLog::add($this->id,MeetingLog::ACTION_REPEAT,$user_id,0);
             return $m->id;
           }

           public function increaseSequence() {
             // increase the meeting sequence_id for iCal ics files
             $this->sequence_id+=1;
             $this->update();
           }

           public function getParticipantStatus($participant_id) {
             // note: shows if participant has declined or been removed
             // does not indicate if they are an organizer
               foreach ($this->participants as $p) {
                 if ($p->participant_id == $participant_id) {
                   return $p->status;
                 }
               }
               // participant not found
               return false;
           }

           public function isSomeoneAvailable() {
             // if all participants declined, removed - then no meeting or finalization possible
             // alternately count participants with STATUS_DEFAULT
             $okay=false;
             foreach ($this->participants as $p) {
               if ($p->status ==Participant::STATUS_DEFAULT) {
                 // one person is available, all is okay
                 $okay = true;
               }
             }
             return $okay;
           }

           public function countAttendingParticipants($includeOrganizer = false) {
             $cnt=0;
             // organizer included
             if ($includeOrganizer) {
                 $cnt = 1;
             }
             foreach ($this->participants as $p) {
               if ($p->status==Participant::STATUS_DEFAULT) {
                 $cnt+=1;
               }
             }
             return $cnt;
           }

       public function isVirtual() {
         if ($this->meeting_type == Meeting::TYPE_PHONE ||
             $this->meeting_type == Meeting::TYPE_VIDEO ||
             $this->meeting_type == Meeting::TYPE_VIRTUAL) {
           return true;
         } else {
           return false;
         }
       }

       public function getSharingUrl() {
         return Url::base(true).'/'.Html::encode($this->owner->username).'/'.$this->identifier;
       }

       public static function friendlyDateFromTimeString($time_str) {
         $tstamp = strtotime($time_str);
         return $this->friendlyDateFromTimeString($tstamp);
       }

        // formatting helpers
        public static function friendlyDateFromTimestamp($tstamp,$timezone = 'America/Los_Angeles',$dateOrder=true,$futureTimeless=false) {
          // adjust for timezone
          if (empty($timezone)) {
            $timezone = 'America/Los_Angeles';
          }
          Yii::$app->formatter->timeZone=$timezone;
          // same day as today?
          if ($dateOrder) {
            if (date('z')==date('z',$tstamp)) {
             $date_str = Yii::t('frontend','Today at ').Yii::$app->formatter->asDateTime($tstamp,'h:mm a');
           }   else {
             $oneWeek = 7 * 24 *3600;
             // start time in future
             if (($tstamp>time()) && $futureTimeless) {
               if (($tstamp-time())< $oneWeek) {
                 // this date at
                 $date_str =Yii::t('frontend','This ').Yii::$app->formatter->asDateTime($tstamp,'E MMM d\' '.Yii::t('frontend','at').'\' h:mm a');
               } else if (($tstamp-time())<($oneWeek*2) && (date('w')< date('w',$tstamp))) {
                 $date_str =Yii::t('frontend','Next ').Yii::$app->formatter->asDateTime($tstamp,'E MMM d\' '.Yii::t('frontend','at').'\' h:mm a');
               } else {
                 // date only
                 $date_str = Yii::$app->formatter->asDateTime($tstamp,'E MMM d');
               }
             } else {
                $date_str = Yii::$app->formatter->asDateTime($tstamp,'E MMM d\' '.Yii::t('frontend','at').'\' h:mm a');
             }

           }
         } else {
           $date_str = Yii::$app->formatter->asDateTime($tstamp,'h:mm a \' '.Yii::t('frontend','on').'\' E MMM d');
         }
          return $date_str;
        }

       public static function prepareDownloadIcs($meeting_id,$actor_id) {
         $m = Meeting::findOne($meeting_id);
         if ($m->isVirtual()) {
           $noPlaces = true;
           $chosenPlace=false;
         } else {
           $noPlaces = false;
           $chosenPlace = $m->getChosenPlace($m->id);
         }
         // chosen time
         $chosenTime=$m->getChosenTime($m->id);
         // Get message header
         $header = $m->getMeetingHeader();
         // build an attendees array of both the organizer and the participants
         // to do - this can be replaced by buildAttendeeList
         // but friendship reciprocate needs to be reviewed and included
         $cnt =0;
         $attendees = array();
         foreach ($m->participants as $p) {
           if ($p->status ==Participant::STATUS_DEFAULT) {
             $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
             $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
             'email'=>$p->participant->email,
             'username'=>$p->participant->username];
             $cnt+=1;
             // reciprocate friendship to organizer
             \frontend\models\Friend::add($p->participant_id,$p->invited_by);
             // to do - reciprocate friendship in multi participant meetings
           }
         }
         $auth_key=\common\models\User::find()->where(['id'=>$m->owner_id])->one()->auth_key;
         $attendees[$cnt]=['user_id'=>$m->owner_id,
           'auth_key'=>$auth_key,
           'email'=>$m->owner->email,
           'username'=>$m->owner->username];
         foreach ($attendees as $cnt=>$a) {
           if ($a['user_id']==$actor_id) {
             $icsPath = Meeting::buildCalendar($m->id,$chosenPlace,$chosenTime,$a,$attendees);
             if (!MiscHelpers::isIphone()) {
               MiscHelpers::downloadFile($icsPath);
             } else {
                Yii::$app->response->redirect(MiscHelpers::buildUrl().'/'.$icsPath);
             }
           }
         }
       }

       public static function buildCalendar($id,$chosenPlace,$chosenTime,$attendee,$attendeeList) {
         $meeting = Meeting::findOne($id);
         $invite = new \common\models\Calendar($id);
         $start_time = $chosenTime->start+(3600*7); // adjust timezone to PST
         $end_time = $chosenTime->end+(3600*7);
         // note below, we send PST time zone with these times
         $sdate = new \DateTime(date("Y-m-d h:i:sA",$start_time), new \DateTimeZone('PST'));
         $edate = new \DateTime(date("Y-m-d h:i:sA",$end_time), new \DateTimeZone('PST'));
         $description = $meeting->message;
         // check if its a confernce with no location
         if ($chosenPlace!==false) {
           if ($chosenPlace->place->website<>'') {
             $description.=' Website: '.$chosenPlace->place->website;
           }
           $location = str_ireplace(',',' ',$chosenPlace->place->name.' '.str_ireplace(', United States','',$chosenPlace->place->full_address));
         } else {
           $location ='';
         }
        $invite
         	->setSubject($meeting->subject)
         	->setDescription($description)
          ->setStart($sdate)
         	->setEnd($edate)
         	->setLocation($location)
         	->setOrganizer($meeting->owner->email, $meeting->owner->username)
          ->setSequence($meeting->sequence_id);
          $commentStr='';
          foreach ($attendeeList as $a) {
            $invite
            ->addAttendee($a['email'], $a['username']);
            // if building for organizer, attach attendee contact info
            // otherwise, attach organizer contact info
              if ($meeting->isVirtual()) {
                if ($a['user_id']<>$meeting->owner_id) {
                  // send organizer contact
                  $commentStr.=UserContact::buildContactString($meeting->owner_id,'ical');
                } else {
                  // send attendee contact
                  $commentStr.=UserContact::buildContactString($a['user_id'],'ical');
                }
            }
          }
          $invite->setComment($commentStr);
          $invite->setUrl(MiscHelpers::buildCommand($id,Meeting::COMMAND_VIEW,0,$attendee['user_id'],$attendee['auth_key']));
          if ($meeting->status ==Meeting::STATUS_CANCELED) {
            $invite->setStatus('CANCELLED');
          } else {
            $invite->setStatus('CONFIRMED');
          }
          $invite->generate() // generate the invite
	         ->save('./invites/','cal_'.$attendee['user_id'].'_'.Yii::$app->getSecurity()->generateRandomString(12).'.ics'); // save it to a file;
           $downloadLink = $invite->getSavedPath();
           return $downloadLink;
       }

       public static function clearLog($id) {
         $mtg = Meeting::findOne($id);
         $mtg->cleared_at = time();
         $mtg->update();
       }

       public static function touchLog($id) {
         $mtg = Meeting::findOne($id);
         $mtg->logged_at = time();
        /* if ($mtg->cleared_at==0)
         {
           $mtg->cleared_at=time()-1;
         }*/
         $mtg->update();
       }

       public static function findFresh() {
         // identify all meetings with log entries not yet cleared
         $meetings = Meeting::find()->where('logged_at-cleared_at>0')->all();
         foreach ($meetings as $m) {
           // to do - choose a different safe gap, for now an hour
           if ((time()-$m->logged_at)>3600) {
             // to do - consider clearing out these old ones
             continue;
           }
           //echo 'M-id: '.$m->id.' '.(time()-$m->logged_at).' <br />';
           // uncleared log entry older than TIMELAPSE, and past planning stage
           if ((time()-$m->logged_at) > MeetingLog::TIMELAPSE && $m->status>=Meeting::STATUS_SENT) { //
             // get logged items which occured after last cleared_at
              $m->notify($m->id,$m->owner_id);
               // notify the participants
               foreach ($m->participants as $p) {
                 // don't update removed and declined participants
                 if ($p->status!=Participant::STATUS_DEFAULT) {
                    continue;
                  }
                  //echo 'Notify P-id: '.$p->participant_id.'<br />';
                  $m->notify($m->id,$p->participant_id);
               }
               // check if meeting has place and time for everyone now
               if (count($m->participants)>1 && !MeetingLog::hasEventOccurred($m->id,MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE) && Meeting::isEveryoneAvailable($m->id)) {
                 Meeting::notifyOrganizers($m->id,MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE);
                 MeetingLog::add($m->id,MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE,0);
               }
               // clear the log for this meeting
               Meeting::clearLog($m->id);
             }
           }
        }

        public static function notifyOrganizers($meeting_id,$event) {
          $m=Meeting::findOne($meeting_id);
          switch ($event) {
            case MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE:
              $subject = $m->getMeetingHeader();
              $p1 = Yii::t('frontend','At least one meeting date time and one place are agreeable to all your meeting participants. You can choose them and finalize the meeting now.');
              $content=[
                'subject' => Yii::t('frontend','Your Meeting is Ready to Finalize'),
                'heading' => Yii::t('frontend','Everyone is Available'),
                'p1' => $p1,
                'p2' => '',
                'plain_text' => $p1.'...'.Yii::t('frontend','Visit the meeting here: '),
              ];
              $button= [
                'text' => Yii::t('frontend','View the Meeting'),
                'command' => Meeting::COMMAND_VIEW,
                'obj_id' => 0,
              ];
            break;
            default;
            // do nothing
              return false;
            break;
          }
          $m->generic_notify(0,$meeting_id,$content,$button,false,true);
        }

       public static function checkAbandoned() {
         // converts sent and planned meetings beyond ABANDONED_AGE to Expired
         // if the last change is old move to past
         $abandoned_time = time() - (3600*24*7*Meeting::ABANDONED_AGE);
         $meetings = Meeting::find()->where('logged_at<'.$abandoned_time)->andWhere(['meeting.status'=>[Meeting::STATUS_SENT,Meeting::STATUS_PLANNING]])->all();
         foreach ($meetings as $m) {
            $m->status = Meeting::STATUS_EXPIRED;
            $m->update();
            MeetingLog::add($m->id,MeetingLog::ACTION_ABANDON_MEETING,$m->owner_id);
           }
       }

       public static function checkPast() {
         // transitions confirmed meetings past their date to completed
         // if the chosen datetime has passed, move to STATUS_COMPLETED
         $meetings = Meeting::find()
          ->where(['status'=>Meeting::STATUS_CONFIRMED])
          ->all();
          //->orWhere(['meeting.status'=>[Meeting::STATUS_SENT,Meeting::STATUS_CONFIRMED]])->all();
         echo ' <br />';
         foreach ($meetings as $m) {
           echo 'Meeting: '.$m->id.' Status: '.$m->status.' <br />';
           echo $m->owner->email.' ('.$m->owner->id.') - '.$m->subject.' <br />';
           $chosenTime=Meeting::getChosenTime($m->id);
           echo (time()-$chosenTime->start).' <= '.time().' -- '.$chosenTime->start.' ==>';
           if (time()>$chosenTime->start) {
             echo 'PAST';
             echo '<br />';
             // chosen meeting time has password_needs_rehash
             if ($m->status == Meeting::STATUS_CONFIRMED) {
                $m->status = Meeting::STATUS_COMPLETED;
                $m->update();
                MeetingLog::add($m->id,MeetingLog::ACTION_COMPLETE_MEETING,$m->owner_id);
             }
           } else {
             echo 'CURRENT';
             echo '<br />';
           }
         }
       }

       public static function displayProfileHints() {
         $user_id = Yii::$app->user->getId();
         $user=User::findOne($user_id);
         if ($user->status==User::STATUS_PASSIVE) {
           if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
               $user->generatePasswordResetToken();
               $user->update();
           }
           $t=$user->password_reset_token;
            Yii::$app->getSession()->setFlash('info', Yii::t('frontend','Please ').HTML::a(Yii::t('frontend','reset your password'),Url::to(['/site/reset-password','token'=>$t],true)).Yii::t('frontend',' or ').Html::a(Yii::t('frontend','link a social account (e.g. Google, Facebook, LinkedIn)'),Url::to(['/user-profile','tab'=>'social'],true)).Yii::t('frontend',' so you can login directly.').'</a>');
         } else {
            $up_id = MiscHelpers::isProfileEmpty($user_id);
            // returns UserProfile->id if available
            if ($up_id!==false) {
              Yii::$app->getSession()->setFlash('info', Yii::t('frontend','Please ').'<a href="' .Url::to(['/user-profile/update','id'=>$up_id],true).'">'.Yii::t('frontend','click here to add your full name').'</a>'.Yii::t('frontend',' so we can share it with meeting participants.'));
            }
         }
       }

       public static function displayNotificationHint($meeting_id) {
         $mtg = Meeting::findOne($meeting_id);
         Yii::$app->session['displayHint']='on';
         if ($mtg->status >= Meeting::STATUS_SENT) {
           Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.'));
         }
       }

       public static function sendLateNotice($meeting_id,$sender_id) {
         // check meeting log to see if it's already been sent for meeting, user_id
         $ml = MeetingLog::find()->where(['meeting_id'=>$meeting_id,'actor_id'=>$sender_id,'action'=>MeetingLog::ACTION_SENT_RUNNING_LATE])->count();
         if ($ml>0) {
           return false;
         }
         $sender_name=MiscHelpers::getDisplayName($sender_id);
         // for all participants and organizer not the $user_id
         $mtg = Meeting::findOne($meeting_id);
         $attendees = $mtg->buildAttendeeList();
         $contacts_html= UserContact::buildContactString($sender_id,'html');
         foreach ($attendees as $a)
         {
          if ($a['user_id']==$sender_id) {
            // don't send late notice to sender
            continue;
          }
          // send the late notice
         // check if email is okay and okay from this sender_id
         if (User::checkEmailDelivery($a['user_id'],$sender_id)) {
           Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($a['user_id']);
             // Build the absolute links to the meeting and commands
             $links=[
               'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
               'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key']),
               'running_late'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_RUNNING_LATE,0,$a['user_id'],$a['auth_key']),
               'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
               'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,$sender_id,$a['user_id'],$a['auth_key']),
               'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
             ];
             // send the message
             $message = Yii::$app->mailer->compose([
               'html' => 'late-html',
               'text' => 'late-text'
             ],
             [
               'meeting_id' => $mtg->id,
               'sender_id'=> $sender_id,
               'user_id' => $a['user_id'],
               'auth_key' => $a['auth_key'],
               'links' => $links,
               'meetingSettings' => $mtg->meetingSettings,
               'sender_name' => $sender_name,
               'contacts_html' => $contacts_html,
           ]);
             if (!empty($a['email'])) {
               $message->setFrom(['support@meetingplanner.io'=>'Meeting Planner']);
               $message->setReplyTo('mp_'.$meeting_id.'@meetingplanner.io');
               $message->setTo($a['email'])
                   ->setSubject(Yii::t('frontend','Meeting Update: '.$sender_name.' is Running Late'))
                   ->send();

               // to do - send running late notice
               // look up user_contact with sms for $user_id
               // send SMS with running late notice

             }
          }
        }
         // add the event
         MeetingLog::add($meeting_id,MeetingLog::ACTION_SENT_RUNNING_LATE,$sender_id);
         return true;
       }

     public static function checkContactInformation($meeting_id) {
       $mtg = Meeting::findOne($meeting_id);
       $user_id = $mtg->owner_id;
       // build an attendees array for all participants without contact information
       $cnt =0;
       $attendees = array();
       foreach ($mtg->participants as $p) {
         if ($p->status ==Participant::STATUS_DEFAULT &&
          UserContact::countContacts($p->participant_id)==0) {
           $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
           $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
           'email'=>$p->participant->email,
           'username'=>$p->participant->username];
           $cnt+=1;
         }
       }
       if (UserContact::countContacts($user_id)==0) {
         // add organizer
         $auth_key=\common\models\User::find()->where(['id'=>$mtg->owner_id])->one()->auth_key;
         $attendees[$cnt]=['user_id'=>$mtg->owner_id,'auth_key'=>$auth_key,
           'email'=>$mtg->owner->email,
           'username'=>$mtg->owner->username];
       }
     // use this code to send
     foreach ($attendees as $cnt=>$a) {
       // check if email is okay and okay from this sender_id
       if (User::checkEmailDelivery($a['user_id'],$user_id)) {
         Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($a['user_id']);
           // Build the absolute links to the meeting and commands
           $links=[
             'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
             'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key']),
             'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
             'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,$user_id,$a['user_id'],$a['auth_key']),
             'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
             'add_contact'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_ADD_CONTACT,0,$a['user_id'],$auth_key)
           ];
           // send the message
           $message = Yii::$app->mailer->compose([
             'html' => 'contact-html',
             'text' => 'contact-text'
           ],
           [
             'meeting_id' => $mtg->id,
             'sender_id'=> $user_id,
             'user_id' => $a['user_id'],
             'auth_key' => $a['auth_key'],
             'links' => $links,
             'meetingSettings' => $mtg->meetingSettings,
         ]);
           // to do - add full name
         $message->setFrom(array('support@meetingplanner.io'=>$mtg->owner->email));
         $message->setReplyTo('mp_'.$mtg->id.'@meetingplanner.io');
         $message->setTo($a['email'])
             ->setSubject(Yii::t('frontend','Meeting Request: Please provide your contact information.'))
             ->send();
             // add to log
             MeetingLog::add($mtg->id,MeetingLog::ACTION_SENT_CONTACT_REQUEST,$a['user_id'],0);
         }
       }
     }

     public static function notify($meeting_id,$user_id) {
       // send updates about recent meeting changes made by $user_id
       $mtg = Meeting::findOne($meeting_id);
       $u = \common\models\User::find()->where(['id'=>$user_id])->one();
       if (empty($u->auth_key)) {
         return false;
       }
       //echo $u->email;
       $a=['user_id'=>$user_id,
        'auth_key'=>$u->auth_key,
        'email'=>$u->email,
        'username'=>$u->username
      ];
       // check if email is okay and okay from this sender_id
       if (User::checkEmailDelivery($user_id,0)) {
           Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($user_id);
           // Build the absolute links to the meeting and commands
           $links=[
             'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
             'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key']),
             'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
             //'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,0,$a['user_id'],$a['auth_key']),
             'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
           ];
           // build the english language notification
           $history = MeetingLog::getHistory($meeting_id,$user_id,$mtg->cleared_at);
           if ($history===false or $history=='') {
             // no recorded events were reportable, skip the email
             return;
           }
           // send the message
           $message = Yii::$app->mailer->compose([
             'html' => 'notify-html',
             'text' => 'notify-text'
           ],
           [
             'meeting_id' => $mtg->id,
             'sender_id'=> $user_id,
             'user_id' => $a['user_id'],
             'auth_key' => $a['auth_key'],
             'links' => $links,
             'meetingSettings' => $mtg->meetingSettings,
             'history'=>$history,
         ]);
           if (!empty($a['email'])) {
             $message->setFrom(['support@meetingplanner.io'=>'Meeting Planner']);
             $message->setReplyTo('mp_'.$meeting_id.'@meetingplanner.io');
             $message->setTo($a['email'])
                 ->setSubject(Yii::t('frontend','Meeting Update: ').$mtg->subject)
                 ->send();
           }
        }
     }

     public static function generic_notify($user_id=0,$meeting_id,$content,$button = false,$ics = false,$organizersOnly=true) {
       // user_id is sender
       // sends to all participants unless $organizersOnly is true
       // sends a generic message based on arguments
       $mtg = Meeting::findOne($meeting_id);
       // build an attendees array for all participants without contact information
       $cnt =0;
       $attendees = array();
       foreach ($mtg->participants as $p) {
           if ($organizersOnly && $p->participant_type != Participant::TYPE_ORGANIZER) {
             // skip non-organizers
             continue;
           }
           $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
           $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
           'email'=>$p->participant->email,
           'username'=>$p->participant->username];
           $cnt+=1;
       }
       // add organizer
       $auth_key=\common\models\User::find()->where(['id'=>$mtg->owner_id])->one()->auth_key;
       $attendees[$cnt]=['user_id'=>$mtg->owner_id,'auth_key'=>$auth_key,
         'email'=>$mtg->owner->email,
         'username'=>$mtg->owner->username];
        if ($ics) {
          // chosen place
          if ($mtg->isVirtual()) {
            $noPlaces = true;
            $chosenPlace=false;
          } else {
            $noPlaces = false;
            $chosenPlace = $mtg->getChosenPlace($meeting_id);
          }
          // chosen time
          $chosenTime=$mtg->getChosenTime($meeting_id);
        }
       // use this code to send
       foreach ($attendees as $cnt=>$a) {
         // check if email is okay and okay from this sender_id
         if ($user_id != $a['user_id'] && User::checkEmailDelivery($a['user_id'],$user_id)) {
           Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($a['user_id']);
             // Build the absolute links to the meeting and commands
             $links=[
               'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
               'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key']),
               'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
               'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,$user_id,$a['user_id'],$a['auth_key']),
               'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
             ];
             if ($button!==false) {
               $links['button_url']=MiscHelpers::buildCommand($mtg->id,$button['command'],$button['obj_id'],$a['user_id'],$a['auth_key']);
               $content['button_text']=$button['text'];
             }
             // send the message
             $message = Yii::$app->mailer->compose([
               'html' => 'generic-html',
               'text' => 'generic-text'
             ],
             [
               'meeting_id' => $mtg->id,
               'sender_id'=> $user_id,
               'user_id' => $a['user_id'],
               'auth_key' => $a['auth_key'],
               'links' => $links,
               'content'=>$content,
               'meetingSettings' => $mtg->meetingSettings,
           ]);
           if ($ics) {
             // include iCalFile for meeting
            $icsPath = Meeting::buildCalendar($meeting_id,$chosenPlace,$chosenTime,$a,$attendees);
            $message->attachContent(file_get_contents($icsPath), ['fileName' => 'meeting.ics', 'contentType' => 'text/calendar']);
            }
             // to do - add full name
           $message->setFrom(array('support@meetingplanner.io'=>'Meeting Planner'));
           $message->setReplyTo('mp_'.$mtg->id.'@meetingplanner.io');
           $message->setTo($a['email'])
               ->setSubject($content['subject'])
               ->send();
           }
         }
     }

     public static function isAttendee($meeting_id,$user_id) {
       $m = Meeting::findOne($meeting_id);
       // are they the organizer?
       if ($m->owner_id == $user_id) {
         return true;
       }
       // are they a participant?
       $p = Participant::find()->where(['meeting_id'=>$meeting_id,'participant_id'=>$user_id])->one();
       if (!is_null($p)) {
         return true;
       }
       return false;
     }

    public static function findEmptyMeeting($user_id) {
      // looks for empty meeting in last seven
      $meetings = Meeting::find()->where(['owner_id'=>$user_id,'status'=>Meeting::STATUS_PLANNING])->limit(7)->orderBy(['id' => SORT_DESC])->all();
      foreach ($meetings as $m) {
        if (!is_null($m) and ($m->subject==Meeting::DEFAULT_SUBJECT || $m->subject=='') and (count($m->participants)==0 && count($m->meetingPlaces)==0 && count($m->meetingTimes)==0)) {
          return $m->id;
        }
      }
      return false;
    }

    public static function isMeetingEmpty($meeting_id) {
      $m = Meeting::find()->where(['id'=>$meeting_id])->one();
      return (!is_null($m) and (count($m->participants)==0 && count($m->meetingPlaces)==0 && count($m->meetingTimes)==0));
    }

    public static function countUserMeetings($user_id) {
      // number of meetings owned or participated in
      return Meeting::find()->joinWith('participants')->where(['owner_id'=>$user_id])->orWhere(['participant_id'=>$user_id])->count();
    }

    public static function defaultSubjectList() {
      $subjects = [
        Yii::t('frontend','Coffee'),
        Yii::t('frontend','Breakfast'),
        Yii::t('frontend','Brunch'),
        Yii::t('frontend','Lunch'),
        Yii::t('frontend','Happy hour'),
        Yii::t('frontend','Drinks'),
        Yii::t('frontend','Dinner'),
        Yii::t('frontend','Catch up'),
        Yii::t('frontend','Meetup'),
        Yii::t('frontend','Review '),
        Yii::t('frontend','Discussion about '),
        Yii::t('frontend','Phone call '),
        Yii::t('frontend','Skype '),
        Yii::t('frontend','Video conference '),
        Yii::t('frontend',Meeting::DEFAULT_SUBJECT),
      ];
      return $subjects;
    }

    public static function lookupStatus($status) {
			switch ($status) {
				case Meeting::STATUS_PLANNING:
					$label = Yii::t('frontend','In planning');
				break;
        case Meeting::STATUS_SENT:
					$label = Yii::t('frontend','Sent');
				break;
        case Meeting::STATUS_EXPIRED:
          $label = Yii::t('frontend','Expired');
        break;
        case Meeting::STATUS_CONFIRMED:
					$label = Yii::t('frontend','Confirmed');
				break;
        case Meeting::STATUS_COMPLETED:
					$label = Yii::t('frontend','Completed');
				break;
        case Meeting::STATUS_CANCELED:
					$label = Yii::t('frontend','Canceled');
				break;
        case Meeting::STATUS_TRASH:
					$label = Yii::t('frontend','Deleted');
				break;
			}
      return $label;
    }

    public static function withinLimit($user_id,$minutes_ago = 180) {
      // how many meetings created by this user in past $minutes_ago
      $cnt = Meeting::find()
        ->where(['owner_id'=>$user_id])
        ->andWhere('created_at>'.(time()-($minutes_ago*60)))
        ->count();
      if ($cnt >= Meeting::NEAR_LIMIT ) {
        return false;
      }
      // check in last DAY_LIMIT
      $cnt = Meeting::find()
        ->where(['owner_id'=>$user_id])
        ->andWhere('created_at>'.(time()-(24*3600)))
        ->count();
      if ($cnt >= Meeting::DAY_LIMIT ) {
          return false;
      }
      return true;
    }

    public static function resend($id) {
      $sender_id = Yii::$app->user->getId();
      // check if within resend limit
      $cnt = MeetingLog::find()
        ->where(['actor_id'=>$sender_id])
        ->andWhere(['meeting_id'=>$id])
        ->andWhere(['action'=>MeetingLog::ACTION_RESEND])
        ->count();
      if ($cnt >= Meeting::RESEND_LIMIT ) {
        return false;
      } else {
        $m = Meeting::findOne($id);
        if ($m->status == Meeting::STATUS_SENT) {
          $m->send($sender_id,true);
          // resend the planning invitation
        } else if ($m->status == Meeting::STATUS_CONFIRMED) {
          // resend the confirmed invitation
          $m->finalize($sender_id,true);
        }
        MeetingLog::add($id,MeetingLog::ACTION_RESEND,$sender_id,0);
        return true;
      }
    }

    public static function isEveryoneAvailable($meeting_id) {
      // check that one place works for everyone attending
      $m = Meeting::findOne($meeting_id);
      $cntAll = $m->countAttendingParticipants(true);
      // count organizer + attending participants
      $mpExists=false;
      $mtExists=true;
      $mps = \frontend\models\MeetingPlace::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($mps as $mp) {
        $cnt=0;
        foreach ($mp->meetingPlaceChoices as $mpc) {
          if ($m->getParticipantStatus($mpc->user_id)!=Participant::STATUS_DEFAULT) {
            // skip withdrawn, declined, removed participants
            continue;
          }
          if ($mpc->status == \frontend\models\MeetingPlaceChoice::STATUS_YES) {
            $cnt+=1;
          }
        }
        if ($cnt >=$cntAll) {
          $mpExists = true;
        }
      }
      $mts = \frontend\models\MeetingTime::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($mts as $mt) {
        $cnt=0;
        foreach ($mt->meetingTimeChoices as $mtc) {
          if ($m->getParticipantStatus($mtc->user_id)!=Participant::STATUS_DEFAULT) {
            // skip withdrawn, declined, removed participants
            continue;
          }
          if ($mtc->status == \frontend\models\MeetingTimeChoice::STATUS_YES) {
            $cnt+=1;
          }
        }
        if ($cnt >=$cntAll) {
          $mtExists = true;
        }
      }
      // at least one time and one place works for everyone attending
      if ($mpExists && $mtExists) {
        return true;
      } else {
        return false;
      }
    }

    public static function getSubject($id) {
      $meeting = Meeting::find()->where(['id' => $id])->one();
      return $meeting->subject;
    }

    // these next two functions are for when only a single place and time exist
    // but none is officially chosen to finalize
    public static function getChosenPlace($meeting_id) {
        $meeting = Meeting::find()->where(['id'=>$meeting_id])->one();
        if ($meeting->isVirtual()) {
          return false;
        }
        $chosenPlace = MeetingPlace::find()->where(['meeting_id' => $meeting_id,'status'=>MeetingPlace::STATUS_SELECTED])->one();
        if (is_null($chosenPlace)) {
          // no chosen place, set it as chosen
          $place = MeetingPlace::find()->where(['meeting_id' => $meeting_id])->one();
          if (is_null($place)) {
            return false;
          }
          $place->status = MeetingPlace::STATUS_SELECTED;
          $place->update();
          $chosenPlace = $place;
        }
        return $chosenPlace;
    }

    public static function getChosenTime($meeting_id) {
        $chosenTime = MeetingTime::find()->where(['meeting_id' => $meeting_id,'status'=>MeetingTime::STATUS_SELECTED])->one();
        if (is_null($chosenTime)) {
          // no chosen Time, set first one as chosen
          $chosenTime = MeetingTime::find()->where(['meeting_id' => $meeting_id])->one();
          if (is_null($chosenTime)) {
            // patches old testing platform in real time (meeting time might not exist)
            $mtg = Meeting::findOne($meeting_id);
            if (is_null($mtg)) return;
            $chosenTime = new MeetingTime;
            $chosenTime->meeting_id = $meeting_id;
            $chosenTime->start = time()+48*3600;
            $chosenTime->duration = 3600;
            $chosenTime->end = $chosenTime->start + 3600;
            $chosenTime->status = MeetingTime::STATUS_SELECTED;
            $chosenTime->suggested_by = $mtg->owner_id;
            $chosenTime->created_at = time();
            $chosenTime->updated_at= time();
            $chosenTime->save();
            // need to create entry
          } else {
            $chosenTime->status = MeetingTime::STATUS_SELECTED;
            $chosenTime->update();
          }
        }
        return $chosenTime;
    }

}
