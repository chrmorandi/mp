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

  public function actionIndex($referred_by='')
  {
    $model = new Impeachment();
    if (!Yii::$app->user->isGuest) {
      if (Impeachment::alreadyGuessed(Yii::$app->user->getId())) {
        // check if they've made a prediction
          return $this->redirect(['impeachment/result']);
      }
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      $model->user_id = Yii::$app->user->getId();
      $model->referrer_id= Yii::$app->security->generateRandomString(12);
      $model->referred_by='';
      if ($referred_by<>'') {
        $refby=Impeachment::find()->where(['referred_by'=>$referred_by])->one();
        if (!is_null($refby)) {
          $model->referred_by=$referred_by;
        }
      }
    } else {
      // not authenticated == guest
        Yii::$app->user->setReturnUrl(Yii::$app->request->url);
        $timezone = 'America/Los_Angeles';
    }
    if ($model->load(Yii::$app->request->post())) {

      $hour = intval(Yii::$app->request->post()['Impeachment']['hour']);
      $estimate = strtotime(Yii::$app->request->post()['Impeachment']['estimate'].' 00:00:00 '.$timezone);
      $model->estimate = $model->daystamp = intval($estimate);
      $model->estimate += ($hour*3600);
      $model->month = date('n',$model->estimate);
      $model->year = date('Y',$model->estimate);
      $model->monthyear = date('n',$model->estimate).'/'.date('Y',$model->estimate);
      if ($model->validate()) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We will let you know when it\'s time to plan your party!'));
        $model->save();
        return $this->redirect(['impeachment/result',['referrer_id'=>$model->referrer_id]]);
      } else {
        //var_dump($model->getErrors());exit;
        // to do set flash
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','There was a problem with your estimate.'));
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
      if (!is_null( Yii::$app->getRequest()->getQueryParam('referrer_id'))) {
        $referrer_id =  Yii::$app->getRequest()->getQueryParam('referrer_id');
      } else {
        $referrer_id ='';
      }
      if (Yii::$app->user->isGuest || (!Impeachment::alreadyGuessed(Yii::$app->user->getId()))) {
        return $this->redirect(Yii::$app->params['site']['url'].'/impeachment/'.$referrer_id);
      }
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      Yii::$app->formatter->timeZone=$timezone;
      $model = Impeachment::find()
        ->where(['user_id'=>Yii::$app->user->getId()])
        ->one();
      $avg = Impeachment::getAverage();
      $daysUntil = (($avg-time())/(24*3600));
      $monthyearStats = Impeachment::getMonthStats();
      $dayStats = Impeachment::getDayStats();
      return $this->render('result',['model'=>$model,'avg'=>$avg,'daysUntil'=>$daysUntil,'monthyearStats'=>$monthyearStats,'dayStats'=>$dayStats]);
    }

}
