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
use common\components\MiscHelpers;

class ImpeachmentController extends \yii\web\Controller
{
  public function behaviors()
  {
      return [
          'verbs' => [
              'class' => VerbFilter::className(),
              'actions' => [
                  'delete' => ['post'],
              ],
          ],
        'access' => [
                      'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
                      'rules' => [
                        // allow authenticated users
                         [
                             'allow' => true,
                             'actions'=>['index','result'],
                             'roles' => ['@'],
                         ],
                        [
                            'allow' => true,
                            'actions'=>['index','result'],
                            'roles' => ['?'],
                        ],
                        // everything else is denied
                      ],
                  ],
      ];
  }

  public function actionIndex()
  {
    $model = new Impeachment();
    if (!Yii::$app->user->isGuest) {
      if (Impeachment::alreadyGuessed(Yii::$app->user->getId())) {
        // check if they've made a prediction
          return $this->redirect(['impeachment/result']);
      }
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      $model->user_id = Yii::$app->user->getId();
      $model->referral_id=0;
    } else {
      // not authenticated == guest
        Yii::$app->user->setReturnUrl(Url::to(['impeachment/index']));
        $timezone = 'America/Los_Angeles';
    }
    if ($model->load(Yii::$app->request->post())) {
      $hour = Yii::$app->request->post()['Impeachment']['hour'];
      $estimate = strtotime(Yii::$app->request->post()['Impeachment']['estimate']);
      $model->estimate = $model->daystamp = $estimate;
      $model->estimate + ($hour*3600);
      $model->month = date('n',$model->estimate);
      $model->year = date('Y',$model->estimate);
      $model->monthyear = date('n',$model->estimate).'/'.date('Y',$model->estimate);
      if ($model->validate()) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We will let you know when it\'s time to plan your party!'));
        $model->save();
        return $this->redirect(['impeachment/result']);
      } else {
        // to do set flash
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','There was a problem with your planning day.'));
      }
    } else {
      return $this->render('index', [
        'model'=>$model,
        'hoursArray'=>$model->hoursArray(),
        'timezone'=>$timezone,
      ]);
    }
  }

    public function actionResult()
    {
      if (Yii::$app->user->isGuest) {
            return $this->redirect(['impeachment/index']);
        }
        return $this->render('result');
    }

}
