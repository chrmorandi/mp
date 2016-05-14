<?php

namespace frontend\controllers;
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
                             'actions'=>[''],
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


    public function actionStore()
    {
        //$mn = new MailgunNotification();
        error_log('from mg:'.json_decode($_POST));
        exit;
    }

}
