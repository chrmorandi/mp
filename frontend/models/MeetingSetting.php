<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "meeting_setting".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $participant_add_place
 * @property integer $participant_add_date_time
 * @property integer $participant_choose_place
 * @property integer $participant_choose_date_time
 * @property integer $participant_finalize
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Meeting $meeting
 */
class MeetingSetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_setting';
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
            [['meeting_id'], 'required'],
            [['meeting_id', 'participant_add_place', 'participant_add_date_time', 'participant_choose_place', 'participant_choose_date_time', 'participant_finalize', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'meeting_id' => Yii::t('app', 'Meeting ID'),
            'participant_add_place' => Yii::t('app', 'Participant Add Place'),
            'participant_add_date_time' => Yii::t('app', 'Participant Add Date Time'),
            'participant_choose_place' => Yii::t('app', 'Participant Choose Place'),
            'participant_choose_date_time' => Yii::t('app', 'Participant Choose Date Time'),
            'participant_finalize' => Yii::t('app', 'Participant Finalize'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }
}
