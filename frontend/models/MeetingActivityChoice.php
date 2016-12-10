<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use frontend\models\MeetingActivity;

/**
 * This is the model class for table "{{%meeting_activity_choice}}".
 *
 * @property integer $id
 * @property integer $meeting_activity_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property MeetingActivity $meetingActivity
 * @property User $user
 */
class MeetingActivityChoice extends \yii\db\ActiveRecord
{
  const STATUS_NO = 0;
  const STATUS_YES = 10;
  const STATUS_UNKNOWN = 20;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meeting_activity_choice}}';
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
            [['meeting_activity_id', 'user_id'], 'required'],
            [['meeting_activity_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['meeting_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => MeetingActivity::className(), 'targetAttribute' => ['meeting_activity_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_activity_id' => Yii::t('frontend', 'Meeting Activity ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function addForNewMeetingActivity($meeting_id,$suggested_by,$meeting_activity_id) {
      // create new MeetingActivityChoice for organizer and participant(s)
      // for this meeting_id and this $meeting_activity_id
      // first, let's add for organizer
      $mtg = Meeting::find()->where(['id'=>$meeting_id])->one();
      $this->add($meeting_activity_id,$mtg->owner_id,$suggested_by);
      // then add for participants
      foreach ($mtg->participants as $p) {
        $this->add($meeting_activity_id,$p->participant_id,$suggested_by);
      }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingActivity()
    {
        return $this->hasOne(MeetingActivity::className(), ['id' => 'meeting_activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function add($meeting_activity_id,$user_id,$suggested_by) {
      $model = new MeetingActivityChoice();
      $model->meeting_activity_id = $meeting_activity_id;
      $model->user_id = $user_id;
      // set initial choice status based if they suggested it themselves
       if ($suggested_by == $user_id) {
          $model->status = self::STATUS_YES;
          MeetingActivity::findOne($meeting_activity_id)->adjustAvailability(1);
        } else {
          $model->status = self::STATUS_UNKNOWN;
        }
      $model->save();
    }

    public static function set($id,$status,$user_id,$bulkMode=false)
    {
      $mac = MeetingActivityChoice::findOne($id);
      if ($mac->user_id==$user_id) {
        $mac->status = $status;
        $mac->save();
        if (!$bulkMode) {
          // log only when not in bulk mode i.e. accept all
          // see setAll for more details
          if ($mac->status==MeetingActivityChoice::STATUS_YES) {
            $command = MeetingLog::ACTION_ACCEPT_ACTIVITY;
            MeetingActivity::findOne($mac->meeting_activity_id)->adjustAvailability(1);
          } else {
            $command = MeetingLog::ACTION_REJECT_ACTIVITY;
            MeetingActivity::findOne($mac->meeting_activity_id)->adjustAvailability(-1);
          }
          MeetingLog::add($mac->meetingActivity->meeting_id,$command,$mac->user_id,$mac->meeting_activity_id);
        }
      } else {
        return false;
      }
      return   $mac->id;
    }

    public static function setAll($meeting_id,$user_id)
    {
      // fetch all meetingActivitys for this meeting
      $meetingActivities = MeetingActivity::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($meetingActivities as $ma) {
        // find mpc for this meetingActivity and user_id
        $machoices = MeetingActivityChoice::find()->where(['meeting_activity_id'=>$ma->id,'user_id'=>$user_id])->all();
        foreach ($machoices as $mac) {
          if ($mac->status != MeetingActivityChoice::STATUS_YES) {
            MeetingActivityChoice::set($mac->id,MeetingActivityChoice::STATUS_YES,$user_id,true);
            MeetingActivity::findOne($ma->id)->adjustAvailability(1);
          }
        }
        // add one log entry in bulk mode
        MeetingLog::add($meeting_id,MeetingLog::ACTION_ACCEPT_ALL_ACTIVITIES,$user_id);
      }
    }
}
