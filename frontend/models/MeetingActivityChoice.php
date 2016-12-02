<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%meeting_activity_choice}}".
 *
 * @property integer $id
 * @property integer $meeting_activity_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property MeetingActivity $meetingActivity
 * @property User $user
 */
class MeetingActivityChoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meeting_activity_choice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_activity_id', 'user_id', 'created_at', 'updated_at'], 'required'],
            [['meeting_activity_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['meeting_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => MeetingActivity::className(), 'targetAttribute' => ['meeting_activity_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_activity_id' => Yii::t('frontend', 'Meeting Activity ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingActivity()
    {
        return $this->hasOne(MeetingActivity::className(), ['id' => 'meeting_activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
