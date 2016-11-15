<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\UserToken;
use frontend\models\Meeting;
use frontend\models\Participant;

class MeetingController extends Controller
{
  /*const STATUS_PLANNING =0;
const STATUS_SENT = 20;
const STATUS_CONFIRMED = 40; // finalized
const STATUS_COMPLETED = 50;
const STATUS_EXPIRED = 55;
const STATUS_CANCELED = 60;
const STATUS_TRASH = 70;*/
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionList($app_id='', $app_key='',$token='',$status=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $meetingsObj = new \stdClass();
      // get user_id from $token
      $user_id =1;
      $meetings = Meeting::find()
        ->joinWith('participants')
        ->where(['owner_id'=>$user_id])
        ->orWhere(['participant_id'=>$user_id])
        ->andWhere(['meeting.status'=>[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT]])
        ->distinct()
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      $meetingsObj =[];
      foreach($meetings as $m) {
        $meetingsObj[]=$m;
      }
      return $meetingsObj;
    }

}
