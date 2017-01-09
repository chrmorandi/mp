<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Meeting;
use frontend\models\MeetingTime;
use frontend\models\MeetingTimeChoice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MeetingTimeChoiceController extends \yii\web\Controller
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
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions'=>['set'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

      public function actionSet($id,$state)
      {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // caution - incoming AJAX type issues with val
        $id=str_replace('mtc-','',$id);
        if ((int)$state == 0 or $state=='false')
        {
          $status = MeetingTimeChoice::STATUS_NO;
        } else {
          $status = MeetingTimeChoice::STATUS_YES;
        }
        MeetingTimeChoice::set($id,$status,Yii::$app->user->getId());
        return $id;
      }

      /**
       * Finds the MeetingTimeChoice model based on its primary key value.
       * If the model is not found, a 404 HTTP exception will be thrown.
       * @param integer $id
       * @return MeetingTime the loaded model
       * @throws NotFoundHttpException if the model cannot be found
       */
      protected function findModel($id)
      {
          if (($model = MeetingTimeChoice::findOne($id)) !== null) {
              return $model;
          } else {
              throw new NotFoundHttpException('The requested page does not exist.');
          }
      }
}
