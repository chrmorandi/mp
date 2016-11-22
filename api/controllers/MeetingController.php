<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\MeetingAPI;
use api\models\Service;

class MeetingController extends Controller
{
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
      return MeetingAPI::meetinglist($token,$status);
    }

    public function actionHistory($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::history($token,$meeting_id);
    }

    public function actionMeetingplaces($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::meetingplaces($token,$meeting_id);
    }

    public function actionMeetingtimes($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::meetingtimes($token,$meeting_id);
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

    public function actionSettings($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::settings($token,$meeting_id);
    }

    public function actionCaption($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::caption($token,$meeting_id);
    }

    public function actionDetails($app_id='', $app_secret='',$token='',$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::details($token,$meeting_id);
    }

    public function actionReminders($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return MeetingAPI::reminders($token);
    }

}
