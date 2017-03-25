<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\DynamicModel;
use common\models\User;
use common\models\Yiigun;
use frontend\models\Friend;
use frontend\models\Meeting;

/**
 * This is the model class for table "participant".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $participant_id
 * @property integer $invited_by
 * @property integer $participant_type
 * @property integer $notify
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $invitedBy
 * @property Meeting $meeting
 * @property User $participant
 */
class Participant extends \yii\db\ActiveRecord
{
    const TYPE_DEFAULT = 0;
    const TYPE_ORGANIZER = 10;

    const NOTIFY_ON = 0;
    const NOTIFY_OFF = 1;

    const STATUS_DEFAULT = 0;
    const STATUS_REMOVED = 90;
    const STATUS_DECLINED = 100;
    const STATUS_DECLINED_REMOVED = 110;

    const MEETING_LIMIT = 25;

    public $email;
    public $firstname;
    public $lastname;
    public $username;
    public $password;
    public $new_email;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'participant';
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
            [['meeting_id'], 'required'],
            [['meeting_id', 'participant_id', 'invited_by', 'status','participant_type', 'notify','created_at', 'updated_at'], 'integer'],
              ['email', 'filter', 'filter' => 'trim'],
              ['email', 'required'],
              //['new_email','email'],
              ['email', 'email', 'checkDNS'=>true, 'enableIDN'=>true,'allowName'=>true],
              //['email', 'email'],
              //['new_email','mailgunValidator'],
            ['participant_id', 'compare','compareAttribute'=>'invited_by','operator'=>'!=','message'=>'You cannot invite yourself.'],
        ];
    }

    public function scenarios()
    {
        $scenarios = [
            'some_scenario' => ['new_email'],
        ];

        return array_merge(parent::scenarios(), $scenarios);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
          if ($insert) {
            if (Participant::find()->where(['meeting_id'=>$this->meeting_id])->count()>=Yii::$app->params['maximumPeople']) {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, no more participants are allowed for this meeting.'));
              return false;
            }
            // check for already attending - prevent dup
            if (Meeting::isAttendee($this->meeting_id,$this->participant_id)) {
              return false;
            }
          }
          return true;
        } else {
          return false;
        }
    }

    public function afterSave($insert,$changedAttributes)
    {
        parent::afterSave($insert,$changedAttributes);
        if ($insert) {
          // if Participant is added
          // add MeetingPlaceChoice & MeetingTimeChoice this participant
          $mt = new MeetingTime;
          $mt->addChoices($this->meeting_id,$this->participant_id);
          $mp = new MeetingPlace;
          $mp->addChoices($this->meeting_id,$this->participant_id);
          $ma = new MeetingActivity;
          $ma->addChoices($this->meeting_id,$this->participant_id);
          MeetingLog::add($this->meeting_id,MeetingLog::ACTION_INVITE_PARTICIPANT,$this->invited_by,$this->participant_id);
          // add participant as a friend of the person who invited them
          Friend::add($this->invited_by,$this->participant_id);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'participant_id' => Yii::t('frontend', 'Participant ID'),
            'invited_by' => Yii::t('frontend', 'Invited By'),
            'status' => Yii::t('frontend', 'Status'),
            'participant_type' => Yii::t('frontend', 'Organizer?'),
            'notify' => Yii::t('frontend', 'Notifications?'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function isOrganizer() {
      return ($this->participant_type == Participant::TYPE_ORGANIZER) ? true : false;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'invited_by']);
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
    public function getParticipant()
    {
        return $this->hasOne(User::className(), ['id' => 'participant_id']);
    }

    public function mailgunValidator($attribute,$params)
    {
          $yg = new Yiigun('public');
          $result = $yg->validate($this->$attribute);
          if ($result->is_valid)
            return false;
          else {
            $str = 'There is a problem with your email address '.$result->address.'.';
            if ($result->did_you_mean<>'') {
                $str.=' Did you mean '.$result->did_you_mean.'?';
            }
            $this->addError($attribute, $str);
          }
    }

    public static function add($meeting_id,$participant_id,$invited_by) {
      $newP = new Participant();
      $newP->meeting_id = $meeting_id;
      $newP->participant_id = $participant_id;
      $newP->invited_by = $invited_by;
      $newP->status = Participant::STATUS_DEFAULT;
      $newP->email = User::findOne($participant_id)->email;
      $newP->save();
    }

    public static function withinLimit($meeting_id) {
      // how many meetingplaces on this meeting
      $cnt = Participant::find()
        ->where(['meeting_id'=>$meeting_id])
        ->count();
      if ($cnt >= Participant::MEETING_LIMIT ) {
        return false;
      }
      return true;
    }

    // used for adding participants via Ajax
    public static function customEmailValidator($email)
    {
      $model = DynamicModel::validateData(compact('email'), [
        ['email', 'filter', 'filter' => 'trim'],
        ['email','required'],
        ['email', 'email', 'allowName'=>true,  'checkDNS'=>true, 'enableIDN'=>true],
      ]);
      if ($model->hasErrors()) {
          // validation fails
          return false;
      } else {
          // validation succeeds
          return true;
      }
    }

    public static function getBestName($str) {
      $nameList = explode(' ',trim($str,' '));
      $cnt=count($nameList);
      if (isset($nameList[0])) {
        $result['first']=$nameList[0];
      }
      if ($cnt>2 && isset($nameList[$cnt-2])) {
        $result['last']=$nameList[$cnt-2];
      } else {
        $result['last']='';
      }
      return $result;
    }
}
