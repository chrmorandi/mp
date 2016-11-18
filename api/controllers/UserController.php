<?php

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
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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

    public function actionError() {
      return Service::fail('unknown error');
    }

    public function actionIndex()
    {
        echo 'index';
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
}
