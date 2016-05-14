<?php

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Yiigun;
use frontend\models\MailgunNotification;

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
      MailgunNotification::process();
    }

    public function actionStore()
    {
      // to do - security clean post url
      if (isset($_POST['message-url'])) {
        $mn = new MailgunNotification();
        $mn->status = MailgunNotification::STATUS_PENDING;
        $temp = str_ireplace('https://api.mailgun.net/v2/','',$_POST['message-url']);
        $temp = str_ireplace('https://api.mailgun.net/v3/','',$temp);
        $mn->url = $temp;
        $mn->save();
        Yii::error('test1: ');
        error_log('test2: ');
      }
    }

}
