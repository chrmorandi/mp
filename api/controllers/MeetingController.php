<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\UserToken;
use api\models\MeetingAPI;
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

    public function beforeAction($action)
    {
      // your custom code here, if you want the code to run before action filters,
      // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
      if (!parent::beforeAction($action)) {
          return false;
      }
      if (Service::verifyAccess(Yii::$app->getRequest()->getQueryParam('app_id'),Yii::$app->getRequest()->getQueryParam('app_secret'))) {
        return true;
      } else {
        echo 'your api keys are from the dark side';
        Yii::$app->end();
      }
    }

    public function actionList($app_id='', $app_secret='',$token='',$status=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::list($token,$status);
    }

    public function actionHistory($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::history($token,$meeting_id);
    }

    public function actionMeetingplaces($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
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
 
    public function actionMeetingplacechoices($app_id='', $app_secret='',$token='',$meeting_place_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::meetingplacechoices($token,$meeting_place_id);
    }

    public function actionMeetingtimechoices($app_id='', $app_secret='',$token='',$meeting_time_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::meetingtimechoices($token,$meeting_time_id);
    }

    public function actionNotes($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::notes($token,$meeting_id);
    }
}
