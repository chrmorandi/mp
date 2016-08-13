<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "request".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $requestor_id
 * @property integer $time_adjustment
 * @property integer $number_seconds
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
  const STATUS_NEW = 0;
  const STATUS_ACCEPTED = 10;

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
            [['meeting_id', 'requestor_id', 'time_adjustment', 'number_seconds', 'meeting_time_id', 'place_adjustment', 'meeting_place_id', 'status', 'created_at', 'updated_at'], 'integer'],
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
            'requestor_id' => Yii::t('frontend', 'Requestor ID'),
            'time_adjustment' => Yii::t('frontend', 'Time Adjustment'),
            'number_seconds' => Yii::t('frontend', 'Number Seconds'),
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

    /**
     * @inheritdoc
     * @return RequestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequestQuery(get_called_class());
    }
}
