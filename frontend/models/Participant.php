<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Yiigun;
use frontend\models\Friend;

/**
 * This is the model class for table "participant".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $participant_id
 * @property integer $invited_by
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
    const STATUS_DEFAULT = 0;
    const STATUS_DECLINED = 100;

    public $email;
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
          // the email attribute should be a valid email address
            ['email', 'email'],
            [['meeting_id', 'participant_id', 'invited_by', 'status', 'created_at', 'updated_at'], 'integer'],
              ['email', 'filter', 'filter' => 'trim'],
              ['email', 'required'],
              ['email', 'email'],
              ['new_email','mailgunValidator'],
//              ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],
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
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
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

    public function addUser() {
      // new participant from meeting invite
      // lookup email as existing user
      // if not exist, create user entry
      // username, email and password
          $user = new User();
          $user->username = $this->username;
          $user->email = $this->email;
          $user->status = User::STATUS_PASSIVE;
          $user->setPassword($this->password);
          $user->generateAuthKey();
          $user->save();
      return $user->id;
    }

    public function mailgunValidator($attribute,$params)
    {
          $yg = new Yiigun();
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
}
