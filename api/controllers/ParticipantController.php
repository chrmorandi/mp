<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
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

/**
 * ParticipantController provides API functionality for participant related tasks
 *
 * @author Jeff Reifman <jeff@meetingplanner.io>
 * @since 0.1
 */
class ParticipantController extends Controller
{
    public $headers;

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

    /**
     * Called beforeAction for each public API method
     *  captures $this->header from http_get_request_headers
     *  checks if app_id is correct
     *
     * @param action $action the controller action
     * @return boolean true if app_id matches, false if it doesn't
     * @throws Exception not yet implemented
     */
    public function beforeAction($action)
    {
      // your custom code here, if you want the code to run before action filters,
      // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
      if (!parent::beforeAction($action)) {
          return false;
      }
      $this->headers = Yii::$app->request->headers;
      if ($this->headers->has('app_id') && $this->headers->get('app_id')==Yii::$app->params['app_id']) {
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
