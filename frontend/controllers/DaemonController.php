<?php

// TO DO: Move to backend controllers when a domain is set up
namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\Meeting;
use frontend\models\MeetingReminder;
use frontend\models\MailgunNotification;
use backend\models\UserData;
use backend\models\HistoricalData;

class DaemonController extends Controller
{

    public function behaviors()
    {
        return [
          'access' => [
              'class' => \yii\filters\AccessControl::className(),
              'only' => ['index','hourly','overnight'],
              'rules' => [
                // allow authenticated users
                 [
                     'allow' => true,
                     'actions'=>['index','fix','overnight'],
                     'roles' => ['@'],
                 ],
                [
                    'allow' => true,
                    'actions'=>['quarter','frequent','overnight'],
                    'roles' => ['?'],
                ],
                // everything else is denied
              ],
                    ],
        ];
    }


  public function actionIndex()
  {
    // to do - remove this, fixed friends list for pre-existing users
    // \frontend\models\Fix::fixPreFriends();

    \frontend\models\Fix::fixPreReminders();
  }


public function actionFrequent() {
  // called every three minutes
  // notify users about fresh changes
  Meeting::findFresh();
  // send meeting reminders that are due
  MeetingReminder::check();
  // process new notifications in the store
  MailgunNotification::process();
}

public function actionQuarter() {
    // called every fifteen minutes
    $m = new Meeting;
    $past = $m->checkPast();
    $past = $m->checkAbandoned();
    // to do - turn off output
  }

  public function actionHourly() {
  	  $current_hour = date('G');
  	  if ($current_hour%6) {
        // every six hours
      }
  	}

  public function actionOvernight() {
      UserData::reset();
      HistoricalData::reset();
      $after = mktime(0, 0, 0, 4, 1, 2016);
      $since = mktime(0, 0, 0, 5, 1, 2016);
      $n=0;
      while ($since < time()) {
        if ($n>60) {exit;}
        UserData::calculate($since,$after);
        //HistoricalData::calculate($since,$after);
        $since+=24*60*60;
        echo $n.'<br/>';
        $n+=1;
      }

  }


    public function actionFix()
    {
      // \frontend\models\Fix::cleanupReminders();
      echo 'complete';
    }
}
