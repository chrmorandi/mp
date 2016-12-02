<?php

namespace frontend\models;

use Yii;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'activity', 'suggested_by', 'created_at', 'updated_at'], 'required'],
            [['meeting_id', 'suggested_by', 'status', 'created_at', 'updated_at'], 'integer'],
            [['activity'], 'string', 'max' => 255],
            [['suggested_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['suggested_by' => 'id']],
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
