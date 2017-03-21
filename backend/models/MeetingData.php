<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use frontend\models\Meeting;
use frontend\models\User;
use frontend\models\UserSetting;

/**
 * This is the model class for table "{{%meeting_data}}".
 *
 * @property integer $meeting_id
* @property integer $owner_id
* @property integer $status
* @property integer $meeting_type
* @property integer $count_places
* @property integer $count_participants
* @property integer $count_times
* @property integer $chosen_time
* @property integer $count_places
* @property integer $chosen_place_id
* @property integer $hour
* @property integer $dayweek
* @property integer $created_at
* @property integer $updated_at
*
 * @property Meeting $meeting
 */
class MeetingData extends \yii\db\ActiveRecord
{

    public $cnt;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meeting_data}}';
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
            [['meeting_id', 'owner_id', 'status'], 'required'],
            [['meeting_id', 'owner_id', 'status', 'count_places', 'count_participants', 'count_times', 'chosen_time', 'chosen_place_id', 'hour', 'dayweek', 'created_at', 'updated_at'], 'integer'],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
         'meeting_id' => Yii::t('frontend', 'Meeting ID'),
         'owner_id' => Yii::t('frontend', 'Owner ID'),
         'status' => Yii::t('frontend', 'Status'),
         'count_places' => Yii::t('frontend', 'Count Places'),
         'count_participants' => Yii::t('frontend', 'Count Participants'),
         'count_times' => Yii::t('frontend', 'Count Times'),
         'chosen_time' => Yii::t('frontend', 'Chosen Time'),
         'count_places' => Yii::t('frontend', 'Count Places'),
         'chosen_place_id' => Yii::t('frontend', 'Chosen Place ID'),
         'hour' => Yii::t('frontend', 'Hour'),
         'dayweek' => Yii::t('frontend', 'Dayweek'),
         'created_at' => Yii::t('frontend', 'Created At'),
         'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    public static function gather() {
      // updates meeting data for all meetings
      //test ids
      $idTestStr='1,277,284,296,297,299,318,340,363,403,404,413,414,415,428,461,474,475,476,477,478,479,480,498,501,503,515,518,560,561,562,571,581,704,705,759,762,879,883,929,930,931,932,1027,1064,1065,1353,1600';
      $idList=explode(',', $idTestStr);
      $meetings = Meeting::find()->all();
      foreach ($meetings as $m) {
        if (in_array($m->owner_id,$idList)) {
          continue;
        }
        $md = MeetingData::find()
            ->where(['meeting_id'=>$m->id])
            ->one();
          if (is_null($md)) {
            $md = new MeetingData();
            $md->meeting_id = $m->id;
            $md->is_activity = 0;
            $md->owner_id = $m->owner_id;
            $md->owner_tz = 0;
            $md->status =0;
            $md->count_participants=0;
            $md->count_times=0;
            $md->count_places=0;
            $md->chosen_time=0;
            $md->hour =0;
            $md->dayweek =0;
            $md->chosen_place_id=0;
            $md->save();
          }
          $cp = Meeting::getChosenPlace($m->id);
          if (is_null($cp) || $cp===false) {
            $md->chosen_place_id=0;
          } else {
            $md->chosen_place_id=$cp->place_id;
          }
          // count participants
          $md->count_participants=count($m->participants);
          // count meetingTimes
          $md->count_times=count($m->meetingTimes);
          // count meetingPlaces
          $md->count_places=count($m->meetingPlaces);
          $md->status = $m->status;
          $md->is_activity = $m->is_activity;
          // collect timezone Data
          $us=UserSetting::find()
            ->where(['user_id'=>$md->owner_id])
            ->one();
          if ($us->has_updated_timezone==0) {
            $md->owner_tz = 'America/Los_Angeles';
          } else {
            $md->owner_tz = $us->timezone;
          }
          // get chosen time and place
          $chosenTime = Meeting::getChosenTime($m->id);
          if (is_null($chosenTime)) {
            $md->chosen_time=0;
            $md->dayweek=0;
            $md->hour=0;
          } else {
            $md->chosen_time=$chosenTime->start;
            $md->dayweek = date('w',$md->chosen_time);
            $dt = new \DateTime("now", new \DateTimeZone($md->owner_tz)); //first argument "must" be a string
            $dt->setTimestamp($md->chosen_time); //adjust the object to correct timestamp
            $md->hour = $dt->format('H');
          }
          $md->update();
      }
    }

    public static function fetch() {
      $data = new \stdClass();
      $data->avgTimes=MeetingData::find()->average('count_times');
      $data->avgPlaces=MeetingData::find()->average('count_places');
      $data->activities=MeetingData::find()
      ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
      ->andWhere(['is_activity'=>Meeting::IS_ACTIVITY])
      ->count();
      $data->no_activity=MeetingData::find()
      ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
      ->andWhere(['is_activity'=>Meeting::NOT_ACTIVITY])
      ->count();
      $data->total=MeetingData::find()
      ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
      ->count();
      $data->dwCount =  new ActiveDataProvider([
        'query' => MeetingData::find()
          ->select(['dayweek,COUNT(*) AS cnt'])
          ->where(['status' => Meeting::STATUS_CONFIRMED])
          ->orWhere(['status' => Meeting::STATUS_COMPLETED])
          ->groupBy(['dayweek'])
          ->orderBy('cnt DESC'),
        ]);
        $data->owner =  new ActiveDataProvider([
          'query' => MeetingData::find()
            ->select(['owner_id,COUNT(*) AS cnt'])
            ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
            ->andWhere('owner_id<>1')
            ->andWhere('owner_id<>5')
            ->andWhere('owner_id<>13')
            ->groupBy(['owner_id'])
            ->having('COUNT(*)>1')
            ->orderBy('cnt DESC'),
          ]);
          $data->user_tz =  new ActiveDataProvider([
            'query' => UserSetting::find()
              ->select(['timezone,COUNT(*) AS cnt'])
              ->groupBy(['timezone'])
              ->orderBy('cnt DESC'),
            ]);
        $data->user_tz =  new ActiveDataProvider([
          'query' => UserSetting::find()
            ->addSelect(['timezone,COUNT(*) AS cnt'])
            ->groupBy(['timezone'])
            ->orderBy('cnt DESC'),
          ]);
        $data->hourofday =  new ActiveDataProvider([
          'query' => MeetingData::find()
            ->select(['hour,COUNT(*) AS cnt'])
            ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
            ->groupBy(['hour'])
            ->orderBy('cnt DESC'),
          ]);
      $data->participants =  new ActiveDataProvider([
        'query' => MeetingData::find()
          ->select(['count_participants,COUNT(*) AS cnt'])
          ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
          ->andWhere('count_participants>0')
          ->groupBy(['count_participants']),
        ]);
        $data->places =  new ActiveDataProvider([
          'query' => MeetingData::find()
            ->select(['chosen_place_id,COUNT(*) AS cnt'])
            ->where(['status' => [Meeting::STATUS_CONFIRMED,Meeting::STATUS_COMPLETED]])
            ->andWhere('chosen_place_id>0')
            ->groupBy(['chosen_place_id'])
            ->having('COUNT(*)>1')
            ->orderBy('cnt DESC'),
          'pagination' => [
          'pageSize' => 20,
          ],
          ]);
      return $data;
    }

}
