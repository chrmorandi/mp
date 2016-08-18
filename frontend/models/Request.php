<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingTime;
use frontend\models\MeetingPlace;
/**
 * This is the model class for table "request".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $requestor_id
 * @property integer $completed_by
 * @property integer $time_adjustment
 * @property integer $alternate_time
 * @property integer $meeting_time_id
 * @property integer $place_adjustment
 * @property integer $meeting_place_id
 * @property string $note
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $requestor
 * @property Meeting $meeting
 */
class Request extends \yii\db\ActiveRecord
{
  const STATUS_OPEN = 0;
  const STATUS_ACCEPTED = 10;
  const STATUS_REJECTED = 20;
  const STATUS_WITHDRAWN = 30;

  const TIME_ADJUST_NONE = 50;
  const TIME_ADJUST_ABIT = 60;
  const TIME_ADJUST_OTHER = 70;

  const PLACE_ADJUST_NONE = 80;
  const PLACE_ADJUST_OTHER = 90;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request';
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
            [['meeting_id', 'requestor_id','completed_by', 'time_adjustment', 'alternate_time', 'meeting_time_id', 'place_adjustment', 'meeting_place_id', 'status'], 'integer'],
            [['note'], 'string'],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
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
            'requestor_id' => Yii::t('frontend', 'Requested by'),
            'completed_by' => Yii::t('frontend', 'Completed by'),
            'time_adjustment' => Yii::t('frontend', 'Time Adjustment'),
            'alternate_time' => Yii::t('frontend', 'Number Seconds'),
            'meeting_time_id' => Yii::t('frontend', 'Meeting Time ID'),
            'place_adjustment' => Yii::t('frontend', 'Place Adjustment'),
            'meeting_place_id' => Yii::t('frontend', 'Meeting Place ID'),
            'note' => Yii::t('frontend', 'Note'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor()
    {
        return $this->hasOne(User::className(), ['id' => 'requestor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    public static function buildSubject($request_id,$include_requestor = true) {
      $r = Request::findOne($request_id);
      $requestor = MiscHelpers::getDisplayName($r->requestor_id);
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      $rtime ='';
      $place = '';
      switch ($r->time_adjustment) {
        case Request::TIME_ADJUST_NONE:
        break;
        case Request::TIME_ADJUST_ABIT:
          $rtime = Meeting::friendlyDateFromTimestamp($r->alternate_time,$timezone);
        break;
        case Request::TIME_ADJUST_OTHER:
          $t = MeetingTime::findOne($r->meeting_time_id);
          if (!is_null($t)) {
              $rtime = Meeting::friendlyDateFromTimestamp($t->start,$timezone);;
          }
        break;
      }
      if ($r->place_adjustment == Request::PLACE_ADJUST_NONE || $r->place_adjustment == 0 && $r->meeting_place_id ==0 ) {
        // do nothing
      } else {
        // get place name
        $place = MeetingPlace::findOne($r->meeting_place_id)->place->name;
      }
      $result = $requestor.Yii::t('frontend',' asked to meet at ');
      if ($rtime=='' && $place =='') {
        $result.=Yii::t('frontend','oops...no changes were requested.');
      } else if ($rtime<>'') {
        $result.=$rtime;
        if ($place<>'') {
          $result.=Yii::t('frontend',' and ');
        }
      }
      if ($place<>'') {
        $result.=$place;
      }
      return $result;
    }

    public function accept() {
      // to do - this will need to change when there are multiple participants
      $this->status = Request::STATUS_ACCEPTED;
      $this->update();
      $m = Meeting::findOne($this->meeting_id);
      if ($m->isOwner(Yii::$app->user->getId())) {
        // they are an organizer
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_ORGANIZER_ACCEPT,Yii::$app->user->getId(),$this->id);
      } else {
        // they are a participant
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_ACCEPT,Yii::$app->user->getId(),$this->id);
      }
      // is there a new time
      switch ($r->time_adjustment) {
        case Request::TIME_ADJUST_ABIT:
          // create a new meeting time with alternate_time
          // mark as selected
          //$rtime = Meeting::friendlyDateFromTimestamp($r->alternate_time,$timezone);
          MeetingTime::addFromRequest($this->id);
        break;
        case Request::TIME_ADJUST_OTHER:
         // mark as selected
          //$t = MeetingTime::findOne($r->meeting_time_id);
          MeetingTime::addFromRequest($this->id);
        break;
      }
      // is there a different place
      if ($r->place_adjustment == Request::PLACE_ADJUST_OTHER || $r->meeting_place_id !=0 ) {
        //$place = MeetingPlace::findOne($r->meeting_place_id)->place->name;
        MeetingPlace::addFromRequest($this->id);
      }
      // Make changes to the Meeting
      $m->increaseSequence();
      // resend the finalization - which also needs to be done for resend invitation
      $m->finalize($m->owner_id,true);
    }

    public function reject() {
      $this->status = Request::STATUS_REJECTED;
      $this->update();
      // to do - check if organizer or participant
      MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_REJECT,Yii::$app->user->getId(),$this->id);
    }

    public function withdraw($id) {
      // check that withdrawee created it
      $r = Request::findOne($id);
      $r->status = Request::STATUS_WITHDRAWN;
      $r->update();
      MeetingLog::add($r->meeting_id,MeetingLog::ACTION_REQUEST_WITHDRAW,Yii::$app->user->getId(),$r->id);
    }

    public function notify() {
      // send notification of request with the link
    }

    public static function countRequestorOpen($meeting_id,$requestor_id) {
      return Request::find()->where(['meeting_id'=>$meeting_id,'requestor_id'=>$requestor_id,'status'=>Request::STATUS_OPEN])->count();
    }

    public static function countOpen($meeting_id) {
      return Request::find()->where(['meeting_id'=>$meeting_id,'status'=>Request::STATUS_OPEN])->count();
    }
    /**
     * @inheritdoc
     * @return RequestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequestQuery(get_called_class());
    }
}
