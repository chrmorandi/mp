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
use common\components\MiscHelpers;

class ParticipantController extends Controller
{
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
      $app_id = Yii::$app->getRequest()->getQueryParam('app_id');
      $app_secret = Yii::$app->getRequest()->getQueryParam('app_secret');
      if (Service::verifyAccess($app_id,$app_secret)) {
        return true;
      } else {
        echo 'your api keys are from the dark side';
        Yii::$app->end();
      }
    }

    public function actionList($app_id='', $app_secret='',$token='',$meeting_id) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      // security: check user of token is in meeting_id
      $participantsObj = new \stdClass();
      // get user_id from $token
      $participants = Participant::find()
        ->where(['meeting_id'=>$meeting_id])
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      $participantsObj=[];
      foreach($participants as $p) {
        $person = new \stdClass();
        $person->user_id = $p->participant->id;
        $person->participant_type = $p->participant_type;
        $person->email = $p->participant->email;
        $person->status = $p->status;
        $person->notify = $p->notify;
        $person->display_name = MiscHelpers:: getDisplayName($person->user_id);
        //var_dump($person);exit;
        $participantsObj[]=$person;
      }
      return $participantsObj;
    }

}
