<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MiscHelpers;
use frontend\models\Participant;

/**
 * This is the model class for table "{{%meeting_activity}}".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property string $activity
 * @property integer $availability
 * @property integer $suggested_by
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $suggestedBy
 * @property Meeting $meeting
 * @property MeetingActivityChoice[] $meetingActivityChoices
 */
class MeetingActivity extends \yii\db\ActiveRecord
{
  const STATUS_SUGGESTED =0;
  const STATUS_SELECTED =10; // the chosen date time
  const STATUS_REMOVED =20;

  const MEETING_LIMIT = 7;

  public $url_prefix;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meeting_activity}}';
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
            [['meeting_id', 'activity', 'suggested_by',], 'required'],
            [['meeting_id', 'suggested_by', 'status', 'created_at', 'updated_at'], 'integer'],
            [['activity'], 'string', 'max' => 255],
            [['suggested_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['suggested_by' => 'id']],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
        ];
    }

    public static function defaultActivityList() {
      $activities = [
        Yii::t('frontend','Bachelor party'),
        Yii::t('frontend','Bachelorette party'),
        Yii::t('frontend','Birthday party'),
        Yii::t('frontend','Breakfast'),
        Yii::t('frontend','Brunch'),
        Yii::t('frontend','Coffee, Tea, Juice Bar, et al.'),
        Yii::t('frontend','Concert'),
        Yii::t('frontend','Counseling'),
        Yii::t('frontend','Cycling'),
        Yii::t('frontend','Dessert'),
        Yii::t('frontend','Dinner'),
        Yii::t('frontend','Dog walking'),
        Yii::t('frontend','Drinks'),
        Yii::t('frontend','Dancing'),
        Yii::t('frontend','Bar'),
        Yii::t('frontend','Movies'),
        Yii::t('frontend','Happy hour'),
        Yii::t('frontend','Hiking'),
        Yii::t('frontend','Lunch'),
        Yii::t('frontend','Meditation'),
        Yii::t('frontend','Netflix and chill'),
        Yii::t('frontend','Party'),
        Yii::t('frontend','Protest'),
        Yii::t('frontend','Theater'),
        Yii::t('frontend','Play board games'),
        Yii::t('frontend','Play scrabble'),
        Yii::t('frontend','Play video games'),
        Yii::t('frontend','Running'),
        Yii::t('frontend','Shopping'),
        Yii::t('frontend','Skiing'),
        Yii::t('frontend','Snowboarding'),
        Yii::t('frontend','Snowshoeing'),
        Yii::t('frontend','Stand up comedy'),
        Yii::t('frontend','Walking'),
        Yii::t('frontend','Watch movies'),
        Yii::t('frontend','Watch sports'),
        Yii::t('frontend','Volunteer'),
        Yii::t('frontend','Yoga'),
      ];
      return $activities;
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'activity' => Yii::t('frontend', 'Activity'),
            'suggested_by' => Yii::t('frontend', 'Suggested By'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
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
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingActivityChoices()
    {
        return $this->hasMany(MeetingActivityChoice::className(), ['meeting_activity_id' => 'id']);
    }

    public static function getActivityStatus($meeting,$viewer_id) {
      // measures availability
      // get an array of textual status of meeting places for $viewer_id
      // Acceptable / Rejected / No response:
      $activityStatus['style'] = [];
      $activityStatus['text'] = [];
      foreach ($meeting->meetingActivities as $ma) {
        // build status for each place
        $acceptableChoice=[];
        $rejectedChoice=[];
        $unknownChoice=[];
        // to do - add meeting_id to MeetingActivityChoice for sortable queries
        foreach ($ma->meetingActivityChoices as $mac) {
          if ($mac->user_id == $viewer_id) continue;
          switch ($mac->status) {
            case MeetingActivityChoice::STATUS_UNKNOWN:
              $unknownChoice[]=$mac->user_id;
            break;
            case MeetingActivityChoice::STATUS_YES:
              $acceptableChoice[]=$mac->user_id;
            break;
            case MeetingActivityChoice::STATUS_NO:
              $rejectedChoice[]=$mac->user_id;
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
          $activityStatus['style'][$ma->id]='success';
        }
        if (count($rejectedChoice)>0) {
          $temp.='Rejected by '.MiscHelpers::listNames($rejectedChoice,true,$cntP).'. ';
          $activityStatus['style'][$ma->id]='danger';
        }
        if (count($unknownChoice)>0) {
          $temp.=Yii::t('frontend','No response from').'&nbsp;'.MiscHelpers::listNames($unknownChoice,true,$cntP,true).'.';
          $activityStatus['style'][$ma->id]='warning';
        }
        $activityStatus['text'][$ma->id]=$temp;
      }
      return $activityStatus;
    }

    public static function withinLimit($meeting_id) {
      // how many meetingtimes added to this meeting
      $cnt = MeetingActivity::find()
        ->where(['meeting_id'=>$meeting_id])
        ->count();
        // per user limit option: ->where(['suggested_by'=>$user_id])
      if ($cnt >= MeetingActivity::MEETING_LIMIT ) {
        return false;
      }
      return true;
    }

    public function afterSave($insert,$changedAttributes)
    {
        parent::afterSave($insert,$changedAttributes);
        if ($insert) {
          // if MeetingActivity is added
          // add MeetingActivityChoice for owner and participants
          $mac = new MeetingActivityChoice;
          $mac->addForNewMeetingActivity($this->meeting_id,$this->suggested_by,$this->id);
          MeetingLog::add($this->meeting_id,MeetingLog::ACTION_SUGGEST_ACTIVITY,$this->suggested_by,$this->id);
        }
    }

    public function adjustAvailability($amount) {
      $this->availability+=$amount;
      $this->update();
    }

    public static function removeActivity($meeting_id,$activity_id)
    {
      $ma = MeetingActivity::find()
        ->where(['meeting_id'=>$meeting_id,'id'=>$activity_id])
        ->one();
      $m = Meeting::findOne($meeting_id);
      if ($m->isOrganizer() || $ma->suggested_by == Yii::$app->user->getId()) {
        $ma->status = MeetingActivity::STATUS_REMOVED;
        $ma->update();
        MeetingLog::add($meeting_id,MeetingLog::ACTION_REMOVE_ACTIVITY,$ma->suggested_by,$activity_id);
        return true;
      } else {
        return false;
      }
    }

    public static function addChoices($meeting_id,$participant_id) {
      $allactivities = MeetingActivity::find()->where(['meeting_id'=>$meeting_id])->all();
      foreach ($allactivities as $ma) {
        MeetingActivityChoice::add($ma->id,$participant_id,0);
      }
    }

}
