<?php

namespace frontend\controllers;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\authclient\ClientInterface;
use frontend\models\Auth;
use frontend\models\Impeachment;
use common\models\LoginForm;

class ImpeachmentController extends \yii\web\Controller
{
  public function behaviors()
  {
      return [
          'access' => [
              'class' => AccessControl::className(),
              'only' => [''],
              'rules' => [
                  [
                      'actions' => ['index'],
                      'allow' => true,
                      'roles' => ['?'],
                  ],
                  [
                      'actions' => ['index','result'],
                      'allow' => true,
                      'roles' => ['@'],
                  ],
              ],
          ],
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'logout' => ['post'],
              ],
          ],
      ];
  }

  public function actionIndex()
  {
    $model = new Impeachment();
    if (!Yii::$app->user->isGuest) {
      $model->user_id = Yii::$app->user->getId();
      $model->referral_id=0;
        // TO DO
        // check if they've made a prediction
        if (1==2) {
          return $this->redirect(['impeachment/results']);
        }

    } else {
      // not authenticated == guest
        Yii::$app->user->setReturnUrl(Url::to(['impeachment/index']));
    }
    if ($model->load(Yii::$app->request->post())) {
      $hour = Yii::$app->request->post()['Impeachment']['hour'];
      $estimate = strtotime(Yii::$app->request->post()['Impeachment']['estimate']);
      $model->estimate = $model->daystamp = $estimate;
      $model->estimate + ($hour*3600);
      $model->month = date('n',$model->estimate);
      if ($model->validate()) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We will let you know when it\'s time to plan your party!'));
        $model->save();
      } else {
        // to do set flash
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','There was a problem with your planning day.'));
      }
    } else {
      return $this->render('index', [
        'model'=>$model,
        'hoursArray'=>$model->hoursArray(),
      ]);
    }
  }

    public function actionResult()
    {
        return $this->render('results');
    }

}
