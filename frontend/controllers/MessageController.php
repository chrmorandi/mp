<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingNote;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Meeting model.
 */
class MessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
          'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'only' => ['notify'],
                        'rules' => [
                          // allow authenticated users
                           [
                               'allow' => true,
                               'actions'=>[''],
                               'roles' => ['@'],
                           ],
                          [
                              'allow' => true,
                              'actions'=>['notify'],
                              'roles' => ['?'],
                          ],
                          // everything else is denied
                        ],
                    ],
        ];
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionNotify()
    {
      echo 'here';
    }
}
