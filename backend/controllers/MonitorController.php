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
      // list of monitoring servers
      $monitors[] = '107.170.233.73';
      // other custom code here
      if (( $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'] ) ||
          in_array($_SERVER['REMOTE_ADDR'], $monitors) ||
          (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin())
          )
       {
         return true;
       } else {
         echo 'Access failure';
        return false; // or false to not run the action
       }
    }

    public function actionList() {
      $m = new Monitor;
      $m->checkMethodList();
    }

    public function actionDb() {
      // check database
      $m = new Monitor;
      $m->checkDb();
    }

    public function actionWeb() {
        $m = new Monitor;
        $m->checkWeb();
    }

    public function actionUsers() {
        $m = new Monitor;
        $m->checkUsers();
    }

    public function actionDaemon() {
      $m = new Monitor;
      $m->checkDaemon();

    }

    public function actionInit() {
      $m = new Monitor;
      $m->checkUsers();
    }

    public function actionReminders() {
      $m = new Monitor;
      $m->checkReminders();
    }

    public function actionStats() {
      $m = new Monitor;
      $m->checkStats();
    }
}
