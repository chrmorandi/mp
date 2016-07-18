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
              'only' => ['index','hourly','overnight','recalc','firewall'],
              'rules' => [
                // allow authenticated users
                 [
                     'allow' => true,
                     'actions'=>['index','fix','overnight','recalc','firewall'],
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
      $since = mktime(0, 0, 0);
      $after = mktime(0, 0, 0, 2, 15, 2016);
      UserData::calculate(false,$after);
      HistoricalData::calculate(false,$after);
    }

    public function actionFix()
    {
      // \frontend\models\Fix::cleanupReminders();
      \frontend\models\Fix::cleanupEmails();
      echo 'complete';
    }

    public function actionFirewall()
    {
      // Test mailgun interface for firewall usage
      $test = Yii::$app->mailer->compose()
          ->setTo(Yii::$app->params['adminEmail'])
          ->setFrom(['support@meetingplanner.io' => 'Meeting Planner'])
          ->setSubject('Firewall Test of Email')
          ->setTextBody('If the firewall for email access is working, you will receive this.')
          ->send();
      echo 'complete';
    }

    public function actionDiagnostics() {
      echo 'PHP Version: '.phpversion();
    }
}
