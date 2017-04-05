<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;
use backend\models\Data;
use backend\models\MeetingData;
use backend\models\UserData;
use backend\models\Domain;

/**
 * Data controller
 */
class DataController extends Controller
{

  // historical
  // sign ups by day
  // meetings created by day
  // meetings finalized by day
  // meetings completed by day
  public function behaviors()
  {
      return [
          'access' => [
              'class' => AccessControl::className(),
              'rules' => [
                  [
                      'allow' => true,
                      'matchCallback' => function ($rule, $action) {
                          return (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin());
                        }
                  ],
              ],
          ],
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'logout' => ['post'],
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

    public function actionRecalc() {
        Data::recalc();
    }

    public function actionCurrent()
    {
      $data = Data::getRealTimeData();
      return $this->render('current', [
          'data' => $data,
      ]);
    }

    public function actionMeetings()
    {
        $data = MeetingData::fetch();
        return $this->render('meetings', [
            'data' => $data,
        ]);
    }

    public function actionUsers()
    {
        $data = UserData::fetch();
        return $this->render('users', [
            'data' => $data,
        ]);
    }

    public function actionDomains() {
      UserData::loadDomains();
    }

    public function actionGather() {
      MeetingData::gather();
    }

    public function actionEmails() {
      // preload blacklist and whitelist emails
       Domain::preload();
       //Domain::cleanseUsers();
    }
}
