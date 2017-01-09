<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use backend\models\UserData;
use frontend\models\Auth;
use frontend\models\Meeting;
use frontend\models\Place;
use frontend\models\Friend;

/*
use frontend\models\Participant;
use frontend\models\Friend;
*/
/**
 * This is the model class for table "historical_data".
 *
 * @property integer $id
 * @property integer $date
 * @property double $percent_own_meeting
 * @property double $percent_own_meeting_last30
 * @property double $percent_invited_own_meeting
 * @property double $percent_participant
 * @property double $percent_participant_last30
 * @property integer $count_users
 * @property integer $count_meetings_completed
 * @property integer $count_meetings_planning
 * @property integer $count_meetings_expired
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
            [['date', 'percent_own_meeting', 'percent_own_meeting_last30', 'percent_invited_own_meeting', 'percent_participant','percent_participant_last30', 'count_users', 'count_meetings_completed', 'count_meetings_planning', 'count_places', 'average_meetings', 'average_friends', 'average_places', 'source_google', 'source_facebook', 'source_linkedin'], 'required'],
            [['average_meetings', 'average_friends', 'average_places','percent_own_meeting', 'percent_own_meeting_last30', 'percent_invited_own_meeting', 'percent_participant','percent_participant_last30','count_meetings_expired'], 'number'],
            [['count_users', 'count_meetings_completed', 'count_meetings_planning', 'count_places',  'source_google', 'source_facebook', 'source_linkedin','date','count_meetings_expired'], 'integer'],
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
            'percent_own_meeting' => Yii::t('backend', '% Created Meeting'),
            'percent_own_meeting_last30' => Yii::t('backend', '% Created Meeting Last30'),
            'percent_invited_own_meeting' => Yii::t('backend', '% Invited & Created Meeting'),
            'percent_participant' => Yii::t('backend', '%Prtcpnt'),
            'percent_participant_last30'=>Yii::t('backend', '%Prtcpnt L30'),
            'count_users' => Yii::t('backend', '#Usrs'),
            'count_meetings_completed' => Yii::t('backend', '#MComp'),
            'count_meetings_planning' => Yii::t('backend', '#MPlan'),
            'count_meetings_expired' => Yii::t('backend', '#MExp'),
            'count_places' => Yii::t('backend', '#Plcs'),
            'average_meetings' => Yii::t('backend', 'Mtgs/Usr'),
            'average_friends' => Yii::t('backend', 'Frnds/Usr'),
            'average_places' => Yii::t('backend', 'Plcs/Usr'),
            'source_google' => Yii::t('backend', '#Goog'),
            'source_facebook' => Yii::t('backend', '#FB'),
            'source_linkedin' => Yii::t('backend', '#LkIn'),
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

    public static function reset() {
      HistoricalData::deleteAll();
    }

    public static function calculate($since = false,$after=0) {
        if ($since === false) {
          $since = mktime(0, 0, 0);
        }
        // create new record for date or update existing
        $hd = HistoricalData::find()->where(['date'=>$since])->one();
        if (is_null($hd)) {
          $hd = new HistoricalData();
          $hd->date = $since;
          $action = 'save';
        } else {
          $action = 'update';
        }
        // calculate  $count_meetings_completed
        $hd->count_meetings_completed = Meeting::find()->where(['status'=>Meeting::STATUS_COMPLETED])->andWhere('created_at<'.$since)->count();
        // calculate  $count_meetings_expired
        $hd->count_meetings_expired = Meeting::find()->where(['status'=>Meeting::STATUS_EXPIRED])->andWhere('created_at<'.$since)->count();
        // calculate  $count_meetings_planning
        $hd->count_meetings_planning = Meeting::find()->where('status<'.Meeting::STATUS_COMPLETED)->andWhere('created_at<'.$since)->count();
        // calculate  $count_places
        $hd->count_places = Place::find()->where('created_at>'.$after)->andWhere('created_at<'.$since)->count();
        // calculate  $source_google
        $hd->source_google = Auth::find()->where(['source'=>'google'])->count();
        // calculate  $source_facebook
        $hd->source_facebook = Auth::find()->where(['source'=>'facebook'])->count();
        // calculate  $source_linkedin
        $hd->source_linkedin = Auth::find()->where(['source'=>'linkedin'])->count();
        // total users
        $total_users = UserData::find()->count();
        // calculate  $count_users
        $hd->count_users = $total_users;
        //User::find()->where('status<>'.User::STATUS_DELETED)->andWhere('created_at>'.$after)->count();
        $total_friends = Friend::find()->where('created_at>'.$after)->andWhere('created_at<'.$since)->count();
        $total_places = Place::find()->where('created_at>'.$after)->andWhere('created_at<'.$since)->count();
        if ($total_users >0) {
          $hd->average_meetings = ($hd->count_meetings_completed+$hd->count_meetings_planning)/$total_users;
          $hd->average_friends = $total_friends/$total_users;
          $hd->average_places =  $total_places/$total_users;
          $hd->percent_own_meeting = UserData::find()->where('count_meetings>0')->count() / $total_users;
          $hd->percent_own_meeting_last30 = UserData::find()->where('count_meetings_last30>0')->count() / $total_users;
          $hd->percent_participant = UserData::find()->where('count_meeting_participant>0')->count() / $total_users;
          $hd->percent_participant_last30 = UserData::find()->where('count_meeting_participant_last30>0')->count() / $total_users;
          $query = (new \yii\db\Query())->from('user_data');
          $sum = $query->sum('invite_then_own');
          $hd->percent_invited_own_meeting=$sum/$total_users;
        }
        if ($action=='save') {
          $hd->save();
        } else {
          $hd->update();
        }
    }
}
