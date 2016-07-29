<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use backend\models\Monitor;
use common\components\MiscHelpers;
/**
 * Monitor controller
 */
class MonitorController extends Controller
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
            'only' => [''],
            'rules' => [
              // allow authenticated users
               [
                   'allow' => true,
                   'actions'=>[''],
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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

    public function actionDb() {
        Monitor::reportOk();
    }

    public function actionWeb() {
        Monitor::reportError();
    }

    public function actionInit() {
        $fullReport = \common\models\User::checkAllUsers();
        if ($fullReport->result) {
          Monitor::reportOk();
        } else {
          Monitor::reportError();
          echo MiscHelpers::br(2);
          foreach ($fullReport->errors as $e) {
            echo $e;
            echo MiscHelpers::br();
          }
        }
    }

    public function actionReminders() {
      $report = \frontend\models\Reminder::statusCheck(false);
      if ($report->result) {
        Monitor::reportOk();
        echo MiscHelpers::br(2);
      } else {
        Monitor::reportError();
        echo MiscHelpers::br(2);
        foreach ($report->errors as $e) {
         echo $e.MiscHelpers::br();
       }
      }
    }



}
