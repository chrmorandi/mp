<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\UserPlace;
use backend\models\UserData;
use backend\models\HistoricalData;

/**
 *
 */
class Monitor extends Model
{

  public static function recalc() {
  }

  public static function reportOk() {
    echo 'OK';
  }

  public static function reportError() {
    echo 'ERROR!';
  }

}
