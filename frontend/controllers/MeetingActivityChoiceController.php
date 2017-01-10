<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Meeting;
use frontend\models\MeetingActivity;
use frontend\models\MeetingActivityChoice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MeetingActivityChoiceController extends \yii\web\Controller
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
      $id=str_replace('mac-','',$id);
      //if (Yii::$app->user->getId()!=$mac->user_id) return false;
      if ((int)$state == 0 or $state=='false')
        $status = MeetingActivityChoice::STATUS_NO;
      else
        $status = MeetingActivityChoice::STATUS_YES;
      //$mac->save();
      MeetingActivityChoice::set($id,$status,Yii::$app->user->getId());
      return $id;
    }

    /**
     * Finds the MeetingActivityChoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingActivityChoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
