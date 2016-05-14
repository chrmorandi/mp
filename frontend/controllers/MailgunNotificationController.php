<?php

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

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
                      'only' => ['store'],
                      'rules' => [
                        // allow authenticated users
                         [
                             'allow' => true,
                             'actions'=>['store'],
                             'roles' => ['@'],
                         ],
                        [
                            'allow' => true,
                            'actions'=>['store'],
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

    public function actionStore()
    {
        //$mn = new MailgunNotification();
        //echo 'hi';
        Yii::error('apple','beta');
        error_log($_POST);
        //error_log('from mg:'.json_decode($_POST));
        exit;
    }

}
