<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MiscHelpers;
/**
 * This is the model class for table "meeting_time".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $start
 * @property integer $duration
 * @property integer $end
 * @property integer $suggested_by
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property MeetingTimeChoice[] $meetingTimeChoices
 *
 * @property Meeting $meeting
 * @property User $suggestedBy
 */
class MeetingTime extends \yii\db\ActiveRecord
{
  const STATUS_SUGGESTED =0;
  const STATUS_SELECTED =10; // the chosen date time

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_time';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'start', 'duration','suggested_by'], 'required'],
            [['meeting_id', 'start','duration','end', 'suggested_by', 'status', 'created_at', 'updated_at'], 'integer'],
            [['start'], 'unique', 'targetAttribute' => ['start','meeting_id'], 'message'=>Yii::t('frontend','This date and time has already been suggested.')],

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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'start' => Yii::t('frontend', 'Start'),
            'suggested_by' => Yii::t('frontend', 'Suggested By'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
          if ($insert) {
            if (MeetingTime::find()->where(['meeting_id'=>$this->meeting_id])->count()>=Yii::$app->params['maximumTimes']) {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, no more dates and times are allowed for this meeting.'));
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
          // if MeetingTime is added
          // add MeetingTimeChoice for owner and participants
          $mtc = new MeetingTimeChoice;
          $mtc->addForNewMeetingTime($this->meeting_id,$this->suggested_by,$this->id);
          MeetingLog::add($this->meeting_id,MeetingLog::ACTION_SUGGEST_TIME,$this->suggested_by,$this->id);
        }
    }

    public function addChoices($meeting_id,$participant_id) {
      $all_times = MeetingTime::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($all_times as $mt) {
        MeetingTimeChoice::add($mt->id,$participant_id,0);
      }
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
    public function getSuggestedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'suggested_by']);
    }

    public function getFormattedStartTime()
    {
        // use yii\i18n\Formatter;

        //return asDatetime($this->start);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingTimeChoices()
    {
        return $this->hasMany(MeetingTimeChoice::className(), [ 'meeting_time_id'=>'id']);
    }

    public static function getWhenStatus($meeting,$viewer_id) {
      // get an array of textual status of meeting times for $viewer_id
      // Acceptable / Rejected / No response:
      $whenStatus = [];
      foreach ($meeting->meetingTimes as $mt) {
        // build status for each time
        $acceptableChoice=[];
        $rejectedChoice=[];
        $unknownChoice=[];
        // to do - add meeting_id to MeetingTimeChoice for sortable queries
        foreach ($mt->meetingTimeChoices as $mtc) {
          if ($mtc->user_id == $viewer_id) continue;
          switch ($mtc->status) {
            case MeetingTimeChoice::STATUS_UNKNOWN:
              $unknownChoice[]=$mtc->user_id;
            break;
            case MeetingTimeChoice::STATUS_YES:
              $acceptableChoice[]=$mtc->user_id;
            break;
            case MeetingTimeChoice::STATUS_NO:
              $rejectedChoice[]=$mtc->user_id;
            break;
          }
        }
        $temp ='';
        // to do - update for multiple participants
        if (count($acceptableChoice)>0) {
          $temp.='Acceptable to '.MiscHelpers::getDisplayName($acceptableChoice[0]);
        } else if (count($rejectedChoice)>0) {
          $temp.='Rejected by '.MiscHelpers::getDisplayName($rejectedChoice[0]);
        } else if (count($unknownChoice)>0) {
          $temp.='No response from '.MiscHelpers::getDisplayName($unknownChoice[0]);
        }
        $whenStatus[$mt->id]=$temp;
      }
      return $whenStatus;
    }

    public static function setChoice($meeting_id,$meeting_time_id,$user_id) {
      // $meeting_time_id needs to be set active
      // other $meeting_time_id for this meeting need to be set inactive
      $mtg=Meeting::find()->where(['id'=>$meeting_id])->one();
      foreach ($mtg->meetingTimes as $mt) {
        if ($mt->id == $meeting_time_id) {
          $mt->status = MeetingTime::STATUS_SELECTED;
        }
        else {
          $mt->status = MeetingTime::STATUS_SUGGESTED;
        }
        $mt->save();
      }
      MeetingLog::add($meeting_id,MeetingLog::ACTION_CHOOSE_TIME,$user_id,$meeting_time_id);
      return true;
    }
}
