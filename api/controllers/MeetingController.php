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
use api\models\MeetingAPI;
use api\models\Service;

/**
 * MeetingController provides API functionality for user related tasks
 *
 * @author Jeff Reifman <jeff@meetingplanner.io>
 * @since 0.1
 */
class MeetingController extends Controller
{
    public $headers;

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

    /**
     * List meetings for a user actionList
     *
     * Here is the description
     *
     * * Markdown style lists function too
     * * Just try this out once
     *
     * The section after the description contains the tags; which provide
     * structured meta-data concerning the given element.
     *
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated
     * @param integer $status in header, refers to Meeting model STATUS_PLANNING, STATUS_SENT, STATUS_CONFIRMED, STATUS_ACTIVE, STATUS_DELETED
     *
     * @return array $meetings on success
     * @throws Exception not yet implemented
     *
     */
    public function actionList($signature, $app_id='',$user_id='',$status=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $status= $this->headers->get('status');
      $arg_str = $user_id.$status;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::meetinglist($user_id,$status);
      } else {
          return false;
      }
    }

    /**
     * List meeting history of events for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request history
     *
     * @return array $history on success
     * @throws Exception not yet implemented
     */
    public function actionHistory($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::history($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List meetingtimes for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request meetingtimes
     *
     * @return array $meetingtimes on success
     * @throws Exception not yet implemented
     */
    public function actionMeetingtimes($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::meetingtimes($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List meetingplaces for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request meetingplaces
     *
     * @return array $meetingplaces on success
     * @throws Exception not yet implemented
     */
    public function actionMeetingplaces($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::meetingplaces($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List notes for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request notes
     *
     * @return array $notes on success
     * @throws Exception not yet implemented
     */
    public function actionNotes($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::notes($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List settings for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request settings
     *
     * @return array $settings on success
     * @throws Exception not yet implemented
     */
    public function actionSettings($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::settings($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List caption for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request settings
     *
     * @return string $caption on success
     * @throws Exception not yet implemented
     */
    public function actionCaption($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      return 'sorry, this API not done yet';
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::caption($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * List details for a meeting
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_id in header, the meeting_id to request details
     *
     * @return string $details on success
     * @throws Exception not yet implemented
     */
    public function actionDetails($signature,$app_id='',$user_id=0,$meeting_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_id= $this->headers->get('meeting_id');
      $arg_str = $user_id.$meeting_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::details($user_id,$meeting_id);
      } else {
          return false;
      }
    }

    /**
     * Get meetingplacechoices for a user and a meeting_place_id
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_place_id in header, the $meeting_place_id to request choice statuses
     *
     * @return array $meetingplacechoices on success
     * @throws Exception not yet implemented
     */
    public function actionMeetingplacechoices($signature,$app_id='',$user_id=0,$meeting_place_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_place_id= $this->headers->get('meeting_place_id');
      $arg_str = $user_id.$meeting_place_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::meetingplacechoices($user_id,$meeting_place_id);
      } else {
          return false;
      }
    }

    /**
     * Get meetingtimechoices for a user and a meeting_time_id
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated, user must be a meeting organizer or participant
     * @param integer $meeting_time_id in header, the $meeting_time_id to request choice statuses
     *
     * @return array $meetingtimechoices on success
     * @throws Exception not yet implemented
     */
    public function actionMeetingtimechoices($signature,$app_id='',$user_id=0,$meeting_time_id=0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $meeting_time_id= $this->headers->get('meeting_time_id');
      $arg_str = $user_id.$meeting_time_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::meetingtimechoices($user_id,$meeting_time_id);
      } else {
          return false;
      }
    }

    /**
     * Get reminders for a user for all their meetings
     *
     * @param string $signature the hash signature for this request, signed with the user's user_token
     * @param string $app_id in header, application id
     * @param integer $user_id in header, refers to owner of user_token to be validated
     *
     * @return array $reminders on success
     * @throws Exception not yet implemented
     */
    public function actionReminders($signature,$app_id='',$user_id=0)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $arg_str = $user_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
          return MeetingAPI::reminders($user_id);
      } else {
          return false;
      }
    }
}
