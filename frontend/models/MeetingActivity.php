<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%meeting_activity}}".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property string $activity
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
        Yii::t('frontend','Birthday party'),
        Yii::t('frontend','Breakfast'),
        Yii::t('frontend','Brunch'),
        Yii::t('frontend','Coffee, Tea, Juice Bar, et al.'),
        Yii::t('frontend','Cycling'),
        Yii::t('frontend','Dessert'),
        Yii::t('frontend','Dinner'),
        Yii::t('frontend','Drinks'),
        Yii::t('frontend','Go dancing'),
        Yii::t('frontend','Go for a walk'),
        Yii::t('frontend','Go to counseling'),
        Yii::t('frontend','Go to a concert'),
        Yii::t('frontend','Go to a party'),
        Yii::t('frontend','Go to a protest'),
        Yii::t('frontend','Go to a show'),
        Yii::t('frontend','Go to the bar'),
        Yii::t('frontend','Go to the movies'),
        Yii::t('frontend','Happy hour'),
        Yii::t('frontend','Hiking'),
        Yii::t('frontend','Lunch'),
        Yii::t('frontend','Meditation'),
        Yii::t('frontend','Netflix and chill'),
        Yii::t('frontend','Play board games'),
        Yii::t('frontend','Play scrabble'),
        Yii::t('frontend','Play video games'),
        Yii::t('frontend','Running'),
        Yii::t('frontend','Skiing'),
        Yii::t('frontend','Snowboarding'),
        Yii::t('frontend','Snowshoeing'),
        Yii::t('frontend','Stand up comedy'),
        Yii::t('frontend','Walk the dogs'),
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
}
