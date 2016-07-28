<?php
// TO DO: Move to backend controllers when a domain is set up

namespace frontend\controllers;
use Yii;
use yii\web\Request;
use yii\data\ActiveDataProvider;
use frontend\models\Daemon;
use frontend\models\DaemonSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\MiscHelpers;
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
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'delete' => ['POST'],
              ],
            ],
          'access' => [
              'class' => \yii\filters\AccessControl::className(),
              'only' => ['index','fix','recalc','firewall','diagnostics'],
              'rules' => [
                // allow authenticated users
                 [
                     'allow' => true,
                     'actions'=>['index','fix','recalc','firewall','diagnostics'],
                     'roles' => ['@'],
                 ],
                [
                    'allow' => true,
                    'actions'=>[''],
                    'roles' => ['?'],
                ],
                // everything else is denied
              ],
            ],
        ];
    }

    // only cron jobs and admins can run this controller's actions
    public function beforeAction($action)
    {
      // your custom code here, if you want the code to run before action filters,
      // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
      if (!parent::beforeAction($action)) {
          return false;
      }
      // other custom code here
      if (( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ) ||
          (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()))
       {
         return true;
       }
      return false; // or false to not run the action
    }


  public function actionIndex()
  {
    // to do - remove this, fixed friends list for pre-existing users
    // \frontend\models\Fix::fixPreFriends();
    // \frontend\models\Fix::fixPreReminders();
    if (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()) {
      $searchModel = new DaemonSearch();
         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
         return $this->render('index', [
             'searchModel' => $searchModel,
             'dataProvider' => $dataProvider,
         ]);
      } else {
        $this->goHome();
      }
  }

public function actionFrequent() {
  // called every three minutes
  // notify users about fresh changes
  Meeting::findFresh();
  Daemon::add(Daemon::ACTION_FREQUENT,Daemon::TASK_FIND_FRESH);
  // send meeting reminders that are due
  MeetingReminder::check();
  Daemon::add(Daemon::ACTION_FREQUENT,Daemon::TASK_REMINDER_CHECK);
  // process new notifications in the store
  MailgunNotification::process();
  Daemon::add(Daemon::ACTION_FREQUENT,Daemon::TASK_MAILGUN_PROCESS);
}

public function actionQuarter() {
    // called every fifteen minutes
    $m = new Meeting;
    $past = $m->checkPast();
    Daemon::add(Daemon::ACTION_QUARTER,Daemon::TASK_CHECK_PAST);
    $past = $m->checkAbandoned();
    Daemon::add(Daemon::ACTION_QUARTER,Daemon::TASK_CHECK_ABANDONED);
    // to do - turn off output
  }

  public function actionHourly() {
      Daemon::add(Daemon::ACTION_HOURLY,Daemon::TASK_DO_NOTHING);
  	  $current_hour = date('G');
  	  if ($current_hour%6) {
        // every six hours
      }
  	}

    public function actionOvernight() {
      $since = mktime(0, 0, 0);
      $after = mktime(0, 0, 0, 2, 15, 2016);
      UserData::calculate(false,$after);
      Daemon::add(Daemon::ACTION_OVERNIGHT,Daemon::TASK_CALC_USER_DATA);
      HistoricalData::calculate(false,$after);
      Daemon::add(Daemon::ACTION_OVERNIGHT,Daemon::TASK_CALC_HISTORICAL_DATA);
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

      public function touchSapi() {
        /*
        echo php_sapi_name(); // always apache2handler, local and remote
        $sapi_name=php_sapi_name().'\n';
        $file = Yii::$app->basePath . '/web/uploads/sapi.txt';
        if (!file_exists($file)) {
          file_put_contents($file, $sapi_name);
          $current ='';
        } else {
          $current = file_get_contents($file);
        }
        $current.=$sapi_name;
        $current.='remote_addr: '.$_SERVER['REMOTE_ADDR'];
        // won't work with wget from cron job
        if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
          $current.='remote_addr is 127.0.0.1\r\n';
        }
        //file_put_contents($file, $current);*/
      }
      protected function findModel($id)
         {
             if (($model = Daemon::findOne($id)) !== null) {
                 return $model;
             } else {
                 throw new NotFoundHttpException('The requested page does not exist.');
             }
         }

       public function actionDiagnostics() {
         echo 'PHP Version: '.phpversion();
         echo MiscHelpers::br();
         //echo phpinfo();
         echo Yii::$app->request->userIP;
         echo MiscHelpers::br();
         //Meeting::checkPast();
         //\frontend\models\Place::getMeetingPlaceCountByUser(1);
         //\frontend\models\MeetingTime::calcPopular();
         /*$user_id = 1;
         $s = new \common\models\Sms();
         $s->transmit($user_id,'Fourth transmit test from MP codebase!');
         */
         $report = \frontend\models\Reminder::statusCheck(false);
         foreach ($report->errors as $e) {
           echo $e.MiscHelpers::br();
         }
      }


}
