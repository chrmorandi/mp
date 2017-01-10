<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\UserPlace;
use frontend\models\Reminder;
use backend\models\UserData;
use backend\models\HistoricalData;
use common\components\MiscHelpers;
/**
 *
 */
class Monitor extends Model
{

public function checkDb() {
  $u = User::findOne(1);
  if (!is_null($u)) {
    Monitor::reportOk();
  } else {
    Monitor::reportError();
  }
  Yii::$app->end();
}

  public function checkWeb() {
      Monitor::reportOk();
      Yii::$app->end();
  }

  public function checkReminders() {
    $report = Reminder::statusCheck(false);
    if ($report->result) {
      Monitor::reportOk();
    } else {
      Monitor::reportError();
      echo MiscHelpers::br(2);
      foreach ($report->errors as $e) {
       echo $e.MiscHelpers::br();
       Yii::$app->end();
     }
    }
  }

  public function checkUsers() {
    $m = new Monitor;
      $fullReport = User::checkAllUsers();
      if ($fullReport->result) {
        $m->reportOk();
      } else {
        $m->reportError();
        echo MiscHelpers::br(2);
        foreach ($fullReport->errors as $e) {
          echo $e;
          echo MiscHelpers::br();
        }
      }
      Yii::$app->end();
  }

  public  function checkStats() {
    // checks nightly data rollups
    $m = new Monitor;
    $hd=HistoricalData::find()
      ->orderBy(['id'=> SORT_DESC])
      ->one();
    $timepast = time()-$hd->date;
    if ($timepast < (24*3600)) {
      $m->reportOk();
    } else {
      $m->reportError();
    }
    Yii::$app->end();
  }

  public function checkDaemon() {
    // checks that meeting logs are regularly processed
    $m = new Monitor;
    // checks recent ten mtgs not completed
    $mtgs=Meeting::find()
      ->where('status<'.Meeting::STATUS_COMPLETED)
      ->orderBy(['id'=> SORT_DESC])
      ->limit(10)
      ->all();
    $lag = true;
    $allzero = true;
    foreach ($mtgs as $mtg) {
      if ($mtg->cleared_at >0) {
        $allzero=false;
        if ((time()-$mtg->cleared_at)<(24*3600)) {
          $lag = false;
        }
      }
    }
    // if all cleared_at are zero
    if ($allzero) {
      $m->reportWarn(Yii::t('backend','ten recent meetings cleared_at = 0'));
    } else if ($lag) {
      // or if any cleared_at older than a day
      $m->reportWarn(Yii::t('backend','one or more meetings not cleared_at in 24 hours'));
    } else {
        $m->reportOk();
    }
  }

  public function checkMethodList() {
    $m = new Monitor();
    $cnt = 0;
    //$class_methods=[];
    $class_methods = \get_class_methods($m);
    foreach ($class_methods as $method_name) {
      if ($cnt>10) {
        break;
      }
      echo $method_name.'<br />';
      $cnt+=1;
    }
    $m->reportOk();
  }

  public static function reportOk() {
    echo Yii::t('backend','ok');
  }

  public static function reportError() {
    echo Yii::t('backend','error!');
  }

  public static function reportWarn($message='') {
    echo Yii::t('backend','warn');
    echo ' '.$message;
  }

}
