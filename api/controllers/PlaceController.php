<?php

namespace api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use api\models\Service;
use api\models\UserToken;
use api\models\PlaceAPI;
use frontend\models\Place;

class PlaceController extends Controller
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
      if (Service::verifyAccess(Yii::$app->getRequest()->getQueryParam('app_id'),Yii::$app->getRequest()->getQueryParam('app_secret'))) {
        return true;
      } else {
        echo 'your api keys are from the dark side';
        Yii::$app->end();
      }
    }

    public function actionGet($app_id='', $app_secret='',$token='',$place_id) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      return PlaceAPI::get($token,$place_id);
    }

}
