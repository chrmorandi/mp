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
 * @property integer $participant_reopen
 * @property integer $participant_request_change
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Meeting $meeting
 */
class MeetingSetting extends \yii\db\ActiveRecord
{
  const SETTING_NO = 0;
  const SETTING_ON = 1; // for checkbox on
  const SETTING_YES = 1; // changed to 1 from 10

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
            [['meeting_id', 'participant_add_place', 'participant_add_date_time', 'participant_choose_place', 'participant_choose_date_time', 'participant_add_activity','participant_choose_activity','participant_finalize', 'participant_reopen', 'participant_request_change','created_at', 'updated_at'], 'integer']
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
            'participant_add_place' => Yii::t('frontend', 'Add place options'),
             'participant_add_date_time' => Yii::t('frontend', 'Add date & time options'),
             'participant_add_activity' => Yii::t('frontend', 'Add activity options'),
             'participant_choose_place' => Yii::t('frontend', 'Choose the place'),
             'participant_choose_date_time' => Yii::t('frontend', 'Choose the date & time'),
             'participant_choose_activity' => Yii::t('frontend', 'Choose the activity'),
             'participant_finalize' => Yii::t('frontend', 'Finalize meetings'),
             'participant_reopen' => Yii::t('frontend', 'Make changes after it\'s been finalized'),
             'participant_request_change' => Yii::t('frontend', 'Request changes after it\'s been finalized'),
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
