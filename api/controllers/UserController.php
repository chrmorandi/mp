<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
namespace api\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\Service;
use api\models\UserAPI;

 /**
  * UserController provides API functionality for user related tasks
  *
  * @author Jeff Reifman <jeff@meetingplanner.io>
  * @since 0.1
  */
  class UserController extends Controller
{

    /**
     * @inheritdoc
     */
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
                  //  'delete' => ['POST'],
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

    public function actionError() {
      return Service::fail('unknown error');
    }

    public function actionTimezone($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::timezone($token);
    }

    public function actionFriends($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::friends($token);
    }

    public function actionFullname($app_id='', $app_secret='',$token='',$user_id)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::fullname($token,$user_id);
    }

    public function actionContacts($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::contacts($token);
    }

    public function actionPlaces($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::places($token);
    }

    public function actionReminders($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::reminders($token);
    }

    public function actionDelete($app_id='', $app_secret='',$token='')
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return UserAPI::delete($token);
    }
}
