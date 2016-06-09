<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
/*
use frontend\models\Meeting;
use frontend\models\Participant;
use frontend\models\UserPlace;
use frontend\models\Friend;
*/
/**
 * This is the model class for table "historical_data".
 *
 * @property integer $id
 * @property string $date
 * @property double $percent_own_meeting
 * @property double $percent_own_meeting_last30
 * @property double $percent_invited_own_meeting
 * @property double $percent_participant
 * @property integer $count_users
 * @property integer $count_meetings_completed
 * @property integer $count_meetings_planning
 * @property integer $count_places
 * @property integer $average_meetings
 * @property integer $average_friends
 * @property integer $average_places
 * @property integer $source_google
 * @property integer $source_facebook
 * @property integer $source_linkedin
 */
class HistoricalData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'historical_data';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'percent_own_meeting', 'percent_own_meeting_last30', 'percent_invited_own_meeting', 'percent_participant', 'count_users', 'count_meetings_completed', 'count_meetings_planning', 'count_places', 'average_meetings', 'average_friends', 'average_places', 'source_google', 'source_facebook', 'source_linkedin'], 'required'],
            [['date'], 'safe'],
            [['percent_own_meeting', 'percent_own_meeting_last30', 'percent_invited_own_meeting', 'percent_participant'], 'number'],
            [['count_users', 'count_meetings_completed', 'count_meetings_planning', 'count_places', 'average_meetings', 'average_friends', 'average_places', 'source_google', 'source_facebook', 'source_linkedin'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'date' => Yii::t('backend', 'Date'),
            'percent_own_meeting' => Yii::t('backend', 'Percent Own Meeting'),
            'percent_own_meeting_last30' => Yii::t('backend', 'Percent Own Meeting Last30'),
            'percent_invited_own_meeting' => Yii::t('backend', 'Percent Invited Own Meeting'),
            'percent_participant' => Yii::t('backend', 'Percent Participant'),
            'count_users' => Yii::t('backend', 'Count Users'),
            'count_meetings_completed' => Yii::t('backend', 'Count Meetings Completed'),
            'count_meetings_planning' => Yii::t('backend', 'Count Meetings Planning'),
            'count_places' => Yii::t('backend', 'Count Places'),
            'average_meetings' => Yii::t('backend', 'Average Meetings'),
            'average_friends' => Yii::t('backend', 'Average Friends'),
            'average_places' => Yii::t('backend', 'Average Places'),
            'source_google' => Yii::t('backend', 'Source Google'),
            'source_facebook' => Yii::t('backend', 'Source Facebook'),
            'source_linkedin' => Yii::t('backend', 'Source Linkedin'),
        ];
    }

    /**
     * @inheritdoc
     * @return HistoricalDataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HistoricalDataQuery(get_called_class());
    }
}
