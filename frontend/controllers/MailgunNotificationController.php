<?php

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Yiigun;

class MailgunNotificationController extends \yii\web\Controller
{
  public function behaviors()
  {
      return [
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'store' => ['post'],
              ],
          ],
        'access' => [
                      'class' => \yii\filters\AccessControl::className(),
                      'only' => ['store','test'],
                      'rules' => [
                        // allow authenticated users
                         [
                             'allow' => true,
                             'actions'=>['store','test'],
                             'roles' => ['@'],
                         ],
                        [
                            'allow' => true,
                            'actions'=>['store','test'],
                            'roles' => ['?'],
                        ],
                        // everything else is denied
                      ],
                  ],
      ];
  }


  public function beforeAction($action)
    {
        if ($this->action->id == 'store') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return true;

    }

    public function actionTest() {
      $yg = new Yiigun();
      $response = $yg->get('https://api.mailgun.net/v2/domains/meetingplanner.io/messages/eyJwIjogZmFsc2UsICJrIjogIjdiYWMyZDNjLTRkNGYtNDIzMy04NDU1LTM3ZmMyMjc1YWRmMiIsICJzIjogIjIwYzNkNWRhY2YiLCAiYyI6ICJ0YW5rczIifQ==');
    }

    public function actionStore()
    {

        //$mn = new MailgunNotification();
        //foreach ($_POST as $k => $p) {
          //error_log($k .'='. $p);
        //}
        error_log($_POST['from']);
        error_log($_POST['message-url']);

        exit;
    }

}
