<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use frontend\models\MeetingTime;

/**
 * This is the model class for table "meeting_time_choice".
 *
 * @property integer $id
 * @property integer $meeting_time_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property MeetingTime $meetingTime
 */
class MeetingTimeChoice extends \yii\db\ActiveRecord
{
  const STATUS_NO = 0;
  const STATUS_YES = 10;
  const STATUS_UNKNOWN = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_time_choice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_time_id', 'user_id'], 'required'],
            [['meeting_time_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'meeting_time_id' => Yii::t('frontend', 'Meeting Time ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingTime()
    {
        return $this->hasOne(MeetingTime::className(), ['id' => 'meeting_time_id']);
    }

    public function addForNewMeetingTime($meeting_id,$suggested_by,$meeting_time_id) {
      // create new MeetingTimeChoice for organizer and participant(s)
      // for this meeting_id and this meeting_time_id
      // first, let's add for organizer
      $mtg = Meeting::find()->where(['id'=>$meeting_id])->one();
      $this->add($meeting_time_id,$mtg->owner_id,$suggested_by);
      // then add for participants
      foreach ($mtg->participants as $p) {
        $this->add($meeting_time_id,$p->participant_id,$suggested_by);
      }
    }

    public static function add($meeting_time_id,$user_id,$suggested_by) {
      $model = new MeetingTimeChoice();
      $model->meeting_time_id = $meeting_time_id;
      $model->user_id = $user_id;
      // set initial choice status based if they suggested it themselves
       if ($suggested_by == $user_id) {
          $model->status = self::STATUS_YES;
          MeetingTime::findOne($meeting_time_id)->adjustAvailability(1);
        } else {
          $model->status = self::STATUS_UNKNOWN;
        }
      $model->save();
    }

    public static function set($id,$status,$user_id,$bulkMode=false)
    {
      $mtc = MeetingTimeChoice::findOne($id);
      if ($mtc->user_id==$user_id) {
        $mtc->status = $status;
        $mtc->save();
        if (!$bulkMode) {
          // log only when not in bulk mode i.e. accept all
          // see setAll for more details
          if ($mtc->status==MeetingTimeChoice::STATUS_YES) {
            $command = MeetingLog::ACTION_ACCEPT_TIME;
            MeetingTime::findOne($mtc->meeting_time_id)->adjustAvailability(1);
          } else {
            $command = MeetingLog::ACTION_REJECT_TIME;
            MeetingTime::findOne($mtc->meeting_time_id)->adjustAvailability(-1);
          }
          MeetingLog::add($mtc->meetingTime->meeting_id,$command,$mtc->user_id,$mtc->meeting_time_id);
        }
      } else {
        return false;
      }
      return   $mtc->id;
    }

    public static function setAll($meeting_id,$user_id)
    {
      // fetch all meetingTimes for this meeting
      $meetingTimes = MeetingTime::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($meetingTimes as $mt) {
        // find mpc for this meetingTime and user_id
        $mtchoices = MeetingTimeChoice::find()->where(['meeting_time_id'=>$mt->id,'user_id'=>$user_id])->all();
        foreach ($mtchoices as $mtc) {
          if ($mtc->status != MeetingTimeChoice::STATUS_YES) {
            MeetingTimeChoice::set($mtc->id,MeetingTimeChoice::STATUS_YES,$user_id,true);
            MeetingTime::findOne($mt->id)->adjustAvailability(1);
          }
        }
        // add one log entry in bulk mode
        MeetingLog::add($meeting_id,MeetingLog::ACTION_ACCEPT_ALL_TIMES,$user_id);
      }
    }
}
