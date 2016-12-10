<?php

namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;
use frontend\models\MeetingPlace;

/**
 * This is the model class for table "meeting_place_choice".
 *
 * @property integer $id
 * @property integer $meeting_place_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property MeetingPlace $meetingPlace
 */
class MeetingPlaceChoice extends \yii\db\ActiveRecord
{
  const STATUS_NO = 0;
  const STATUS_YES = 10;
  const STATUS_UNKNOWN = 20;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_place_choice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_place_id', 'user_id'], 'required'],
            [['meeting_place_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'meeting_place_id' => Yii::t('frontend', 'Meeting Place ID'),
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
    public function getMeetingPlace()
    {
        return $this->hasOne(MeetingPlace::className(), ['id' => 'meeting_place_id']);
    }

    public function addForNewMeetingPlace($meeting_id,$suggested_by,$meeting_place_id) {
      // create new MeetingPlaceChoice for organizer and participant(s)
      // for this meeting_id and this meeting_place_id
      // first, let's add for organizer
      $mtg = Meeting::find()->where(['id'=>$meeting_id])->one();
      $this->add($meeting_place_id,$mtg->owner_id,$suggested_by);
      // then add for participants
      foreach ($mtg->participants as $p) {
        $this->add($meeting_place_id,$p->participant_id,$suggested_by);
      }
    }

    public static function add($meeting_place_id,$user_id,$suggested_by) {
      $model = new MeetingPlaceChoice();
      $model->meeting_place_id = $meeting_place_id;
      $model->user_id = $user_id;
      // set initial choice status based if they suggested it themselves
       if ($suggested_by == $user_id) {
          $model->status = self::STATUS_YES;
          MeetingPlace::findOne($meeting_place_id)->adjustAvailability(1);
        } else {
          $model->status = self::STATUS_UNKNOWN;
        }
      $model->save();
    }

    public static function set($id,$status,$user_id = 0,$bulkMode=false)
    {
      $mpc = MeetingPlaceChoice::findOne($id);
      if ($mpc->user_id==$user_id) {
        $mpc->status = $status;
        $mpc->save();
        if (!$bulkMode) {
          // log only when not in bulk mode i.e. accept all
          // see setAll for more details
          if ($mpc->status==MeetingPlaceChoice::STATUS_YES) {
            $command = MeetingLog::ACTION_ACCEPT_PLACE;
            MeetingPlace::findOne($mpc->meeting_place_id)->adjustAvailability(1);
          } else {
            $command = MeetingLog::ACTION_REJECT_PLACE;
            MeetingPlace::findOne($mpc->meeting_place_id)->adjustAvailability(-1);
          }
          MeetingLog::add($mpc->meetingPlace->meeting_id,$command,$mpc->user_id,$mpc->meeting_place_id);
        }
        return $mpc->id;
      } else {
        return false;
      }
    }

    public static function setAll($meeting_id,$user_id)
    {
      // fetch all meetingPlaces for this meeting
      $meetingPlaces = MeetingPlace::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($meetingPlaces as $mp) {
        // find mpc for this meetingPlace and user_id
        $mpchoices = MeetingPlaceChoice::find()->where(['meeting_place_id'=>$mp->id,'user_id'=>$user_id])->all();
        foreach ($mpchoices as $mpc) {
          if ($mpc->status != MeetingPlaceChoice::STATUS_YES) {
            MeetingPlaceChoice::set($mpc->id,MeetingPlaceChoice::STATUS_YES,$user_id,true);
            MeetingPlace::findOne($mp->id)->adjustAvailability(+1);
          }
        }
        // add one log entry in bulk mode
        MeetingLog::add($meeting_id,MeetingLog::ACTION_ACCEPT_ALL_PLACES,$user_id);
      }
    }
}
