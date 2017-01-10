<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\UserPlace;
use backend\models\UserData;
use backend\models\HistoricalData;

/**
 *
 */
class Data extends Model
{

  public static function recalc() {
    return;
    /// currently turned off in production
    set_time_limit(0);
    UserData::reset();
    HistoricalData::reset();
    $after = mktime(0, 0, 0, 2, 19, 2015);
    $since = mktime(0, 0, 0, 2, 20, 2015);
    while ($since < time()) {
      UserData::calculate($since,$after);
      HistoricalData::calculate($since,$after);
      // increment a day
      $since+=24*60*60;
    }
  }

  public static function getRealTimeData() {
    $data = new \stdClass();

    $data->meetings =  new ActiveDataProvider([
      'query' => Meeting::find()
      ->select(['status,COUNT(*) AS dataCount'])
      //->where('approved = 1')
      ->groupBy(['status']),
      'pagination' => [
      'pageSize' => 20,
      ],
      ]);

    $data->totalUsers = User::find()->count();
    $data->users = new ActiveDataProvider([
      'query' => User::find()
      ->select(['status,COUNT(*) AS dataCount'])
      ->groupBy(['status']),
      'pagination' => [
      'pageSize' => 20,
      ],
      ]);

    $data->userPlaces = new ActiveDataProvider([
      'query' => UserPlace::find()
      ->select(['user_id,count(*) AS dataCount'])
      ->where('user_id>1')
      ->groupBy(['user_id'])
      ->limit(5),
      'pagination' => false,
      ]);

    // calculate average # of places per user
    $user_places = UserPlace::find()
      ->select(['user_id,count(*) AS dataCount'])
      ->where('user_id>1')
      ->groupBy(['user_id'])
      ->all();

      $totalUsers = 0;
      $totalPlaces = 0;
      foreach ($user_places as $up) {
        $totalUsers+=1;
        $totalPlaces+=$up->dataCount;
      }
      $data->avgUserPlaces = $totalPlaces / $totalUsers;

      return $data;
  }

  public function cleanupDatabase() {
    $baseUrl = Url::home(true);
    if (stristr($baseUrl,'localhost')===false || \Yii::$app->user->isGuest && !User::findOne(Yii::$app->user->getId())->isAdmin()) {
      Yii::$app->end();
    }

    $mts = Meeting::find()
      ->orderBy(['id'=> SORT_DESC])
      ->limit(7)
      ->all();
    foreach ($mts as $m) {
      echo 'Deleting '.$m->id.' '.$m->subject.'<br />';
      $m->delete();
    }
  }

}
