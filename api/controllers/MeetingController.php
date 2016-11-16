<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\UserToken;
use frontend\models\Meeting;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
use frontend\models\Participant;
use api\models\Service;

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

    public function actionList($app_id='', $app_secret='',$token='',$status=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if (!Service::verifyAccess($app_id,$app_secret)) {
        // to do - error msg
        return false;
      }
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

    public function actionMeetingplaces($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      // security: check user of token is in meeting_id
      if (!Service::verifyAccess($app_id,$app_secret)) {
        // to do - error msg
        return false;
      }
      $meetingsObj = new \stdClass();
      // get user_id from $token
      $user_id =1;
      $places = MeetingPlace::find()
        ->where(['meeting_id'=>$meeting_id])
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      $placesObj =[];
      foreach($places as $p) {
        $x = new \stdClass();
        $x->id = $p->id;
        $x->place_id = $p->place_id;
        $x->suggested_by = $p->suggested_by;
        $x->status = $p->status;
        $x->availability = $p->availability;
        $x->created_at = $p->created_at;
        $x->updated_at = $p->updated_at;
        $x->name = $p->place->name;
        $placesObj[]=$x;
      }
      return $placesObj;
    }

    public function actionMeetingtimes($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      // security: check user of token is in meeting_id
      if (!Service::verifyAccess($app_id,$app_secret)) {
        // to do - error msg
        return false;
      }
      $meetingsObj = new \stdClass();
      // get user_id from $token
      $user_id =1;
      $times = Meetingtime::find()
        ->where(['meeting_id'=>$meeting_id])
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      $timesObj =[];
      foreach($times as $t) {
        $timesObj[]=$t;
      }
      return $timesObj;
    }
}
