<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Meeting;
use frontend\models\MeetingPlace;
use frontend\models\MeetingPlaceChoice;
use frontend\models\Place;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MeetingPlaceChoiceController extends \yii\web\Controller
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
      $id=str_replace('mpc-','',$id);
      //if (Yii::$app->user->getId()!=$mpc->user_id) return false;
      if ((int)$state == 0 or $state=='false')
        $status = MeetingPlaceChoice::STATUS_NO;
      else
        $status = MeetingPlaceChoice::STATUS_YES;
      //$mpc->save();
      MeetingPlaceChoice::set($id,$status,Yii::$app->user->getId());
      return $id;
    }

    /**
     * Finds the MeetingPlaceChoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingPlace the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingPlaceChoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
