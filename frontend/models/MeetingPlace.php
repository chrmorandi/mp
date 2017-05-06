<?php
namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;
use common\components\MiscHelpers;
use frontend\models\Participant;
/**
 * This is the model class for table "meeting_place".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $place_id
 * @property integer $suggested_by
 * @property integer $status
 * @property integer $availability
 * @property integer $created_at
 * @property integer $updated_at
 * @property MeetingPlaceChoice[] $meetingPlaceChoices
 *
 * @property Meeting $meeting
 * @property Place $place
 * @property User $suggestedBy
 */
class MeetingPlace extends \yii\db\ActiveRecord
{
    const STATUS_SUGGESTED =0;
    const STATUS_SELECTED =10;  // the chosen place
    const STATUS_REMOVED =20;

    const MEETING_LIMIT = 7;

    public $searchbox; // for google place search
    public $name;
    public $google_place_id;
    public $location;
    public $website;
    public $vicinity;
    public $full_address;
    public $place_name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'place_id', 'suggested_by'], 'required'],
            [['meeting_id', 'place_id', 'suggested_by', 'status', 'availability','created_at', 'updated_at'], 'integer'],
            [['place_id'], 'unique', 'targetAttribute' => ['place_id','meeting_id'], 'message'=>Yii::t('frontend','This place has already been suggested.')],
            //[['google_place_id'], 'validate_chosen_place'], // ,'skipOnEmpty'=> false
        ];
    }

/*    function validate_chosen_place($attribute, $param) {
        if($this->$attribute<>'' && $this->place_id>0)
            $this->addError($attribute, Yii::t('frontend','Please choose one or the other'));
        }
        */

    public function afterSave($insert,$changedAttributes)
    {
        parent::afterSave($insert,$changedAttributes);
        if ($insert) {
          // if MeetingPlace is added
          // add MeetingPlaceChoice for owner and participants
          $mpc = new MeetingPlaceChoice;
          $mpc->addForNewMeetingPlace($this->meeting_id,$this->suggested_by,$this->id);
          MeetingLog::add($this->meeting_id,MeetingLog::ACTION_SUGGEST_PLACE,$this->suggested_by,$this->place_id);
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
          if ($insert) {
            if (MeetingPlace::find()->where(['meeting_id'=>$this->meeting_id])->count()>=Yii::$app->params['maximumPlaces']) {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, no more places are allowed for this meeting.'));
              return false;
            }
          }
          return true;
        } else {
          return false;
        }
    }

    public static function addChoices($meeting_id,$participant_id) {
      $all_places = MeetingPlace::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($all_places as $mp) {
        MeetingPlaceChoice::add($mp->id,$participant_id,0);
      }
    }

    public static function removePlace($meeting_id,$place_id)
    {
      $mp = MeetingPlace::find()
        ->where(['meeting_id'=>$meeting_id,'place_id'=>$place_id])
        ->one();
      $m = Meeting::findOne($meeting_id);
      if ($m->isOrganizer() || $mp->suggested_by == Yii::$app->user->getId()) {
        $mp->status = MeetingPlace::STATUS_REMOVED;
        $mp->update();
        MeetingLog::add($meeting_id,MeetingLog::ACTION_REMOVE_PLACE,$mp->suggested_by,$place_id);
        return true;
      } else {
        return false;
      }
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
            'place_id' => Yii::t('frontend', 'Place ID'),
            'suggested_by' => Yii::t('frontend', 'Suggested By'),
            'status' => Yii::t('frontend', 'Status'),
            'availability' => Yii::t('frontend', 'Availability'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(Place::className(), ['id' => 'place_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuggestedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'suggested_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingPlaceChoices()
    {
        return $this->hasMany(MeetingPlaceChoice::className(), [ 'meeting_place_id'=>'id']);
    }

    public static function getWhereStatus($meeting,$viewer_id) {
      // measures availability
      // get an array of textual status of meeting places for $viewer_id
      // Acceptable / Rejected / No response:
      $whereStatus['style'] = [];
      $whereStatus['text'] = [];
      foreach ($meeting->meetingPlaces as $mp) {
        // build status for each place
        $acceptableChoice=[];
        $rejectedChoice=[];
        $unknownChoice=[];
        // to do - add meeting_id to MeetingPlaceChoice for sortable queries
        foreach ($mp->meetingPlaceChoices as $mpc) {
          if ($mpc->user_id == $viewer_id) continue;
          switch ($mpc->status) {
            case MeetingPlaceChoice::STATUS_UNKNOWN:
              $unknownChoice[]=$mpc->user_id;
            break;
            case MeetingPlaceChoice::STATUS_YES:
              $acceptableChoice[]=$mpc->user_id;
            break;
            case MeetingPlaceChoice::STATUS_NO:
              $rejectedChoice[]=$mpc->user_id;
            break;
          }
        }
        // to do - integrate current setting for this user in style setting
        $temp ='';
        // count those still in attendance
        $cntP = Participant::find()
  				->where(['meeting_id'=>$meeting->id])
          ->andWhere(['status'=>Participant::STATUS_DEFAULT])
  				->count()+1;
        if (count($acceptableChoice)>0) {
          $temp.='Acceptable to '.MiscHelpers::listNames($acceptableChoice,true,$cntP).'. ';
          $whereStatus['style'][$mp->place_id]='success';
        }
        if (count($rejectedChoice)>0) {
          $temp.='Rejected by '.MiscHelpers::listNames($rejectedChoice,true,$cntP).'. ';
          $whereStatus['style'][$mp->place_id]='danger';
        }
        if (count($unknownChoice)>0) {
          $temp.=Yii::t('frontend','No response from').'&nbsp;'.MiscHelpers::listNames($unknownChoice,true,$cntP,true).'.';
          $whereStatus['style'][$mp->place_id]='warning';
        }
        $whereStatus['text'][$mp->place_id]=$temp;
      }
      return $whereStatus;
    }

    public static function setChoice($meeting_id,$meeting_place_id,$user_id) {
      // meeting_place_id needs to be set active
      // other meeting_place_id for this meeting need to be set inactive
      $mtg=Meeting::find()->where(['id'=>$meeting_id])->one();
      foreach ($mtg->meetingPlaces as $mp) {
        if ($mp->id == $meeting_place_id) {
          $mp->status = MeetingPlace::STATUS_SELECTED;
        }
        else {
          $mp->status = MeetingPlace::STATUS_SUGGESTED;
        }
        $mp->save();
      }
      MeetingLog::add($meeting_id,MeetingLog::ACTION_CHOOSE_PLACE,$user_id,$meeting_place_id);
      return true;
    }

    public static function withinLimit($meeting_id) {
      // how many meetingplaces on this meeting
      $cnt = MeetingPlace::find()
        ->where(['meeting_id'=>$meeting_id])
        ->count();
        // per user limit option: ->where(['suggested_by'=>$user_id])
      if ($cnt >= MeetingPlace::MEETING_LIMIT ) {
        return false;
      }
      return true;
    }

    public function addFromRequest($request_id) {
      // load the request
      $r = \frontend\models\Request::findOne($request_id);
      $m = Meeting::findOne($r->meeting_id);
      // get the duration of the currently accepted MeetingTime
      $mp = new MeetingPlace();
      $mp->meeting_id = $r->meeting_id;
      $mp->place_id = $r->meeting_place_id;
      $mp->suggested_by = $r->completed_by;
      $mp->status = MeetingPlace::STATUS_SELECTED;
      $mp->save();
      MeetingPlace::setChoice($r->meeting_id,$mp->id,$r->completed_by);
    }

    public function adjustAvailability($amount) {
      $this->availability+=$amount;
      $this->update();
    }
}
