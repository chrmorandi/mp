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
              'only' => ['index','fix','recalc','firewall','diagnostics','quarter','frequent','hourly'],
              'rules' => [
                // allow authenticated users
                [
                    'allow' => true,
                    'matchCallback' => function ($rule, $action) {
                        return (1==7);
                      }
                ],
                 [
                     'allow' => true,
                     'actions'=>['index','fix','recalc','firewall','diagnostics','frequent'],
                     'roles' => ['@'],
                 ],
                [
                    'allow' => true,
                    'actions'=>['quarter','frequent','hourly'],
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
  echo php_sapi_name(); // apache2handler
  $this->touchSapi();
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
      //echo 'complete';
    }

    public function actionDiagnostics() {
      echo 'PHP Version: '.phpversion();
      $this->touchSapi();

      //\frontend\models\Place::getMeetingPlaceCountByUser(1);
      //\frontend\models\MeetingTime::calcPopular();
      /*$user_id = 1;
      $s = new \common\models\Sms();
      $s->transmit($user_id,'Fourth transmit test from MP codebase!');
      */
      }

      public function touchSapi() {
        echo php_sapi_name(); // apache2handler
        $sapi_name=php_sapi_name().'\n';
        $file = Yii::$app->basePath . '/web/uploads/sapi.txt';
        if (!file_exists($file)) {
          file_put_contents($file, $sapi_name);
          $current ='';
        } else {
          $current = file_get_contents($file);
        }
        $current.=$sapi_name;


        $isCLI = ( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] );
        if( !$isCLI ) {
          $current.='remote addr != server addr\n';
        } else {
          $current.='remote and server match\n';
        }
        $current.='remote_addr: '.$_SERVER['REMOTE_ADDR'];
        if (!empty($_SERVER['REMOTE_ADDR'])){
            $current.=' remote_addr NOT EMPTY';
          }
        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
          $current.='remote_addr is 127.0.0.1\r\n';
        }




        file_put_contents($file, $current);
      }
}
