<?php

namespace frontend\controllers;

use Yii;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingTime;
use frontend\models\MeetingLog;
use frontend\models\MeetingTimeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use yii\web\Response;

/**
 * MeetingTimeController implements the CRUD actions for MeetingTime model.
 */
class MeetingTimeController extends Controller
{
    const STATUS_PROPOSED = 0;
    const STATUS_SELECTED = 10;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'remove' => ['post'],
                ],
            ],
            'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'rules' => [
                            // allow authenticated users
                            [
                                'allow' => true,
                                'actions' => ['create','update','delete','choose','view','remove'],
                                'roles' => ['@'],
                            ],
                            // everything else is denied
                        ],
                    ],
        ];
    }

    /**
     * Displays a single MeetingTime model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
        return $this->render('view', [
            'model' => $this->findModel($id),
            'timezone'=>$timezone,
        ]);
    }

    /**
     * Creates a new MeetingTime model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($meeting_id)
    {
      if (!MeetingTime::withinLimit($meeting_id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of date times per meeting. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
      }
      //Yii::$app->response->format = Response::FORMAT_JSON;
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      date_default_timezone_set($timezone);
      $mtg = new Meeting();
      $title = $mtg->getMeetingTitle($meeting_id);
      $model = new MeetingTime();
      $model->tz_current = $timezone;
      $model->duration = 1;
      $model->meeting_id= $meeting_id;
      $model->suggested_by= Yii::$app->user->getId();
      $model->status = self::STATUS_PROPOSED;
      //if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {}
        if ($model->load(Yii::$app->request->post())) {
          if (empty($model->start)) {
            $model->start = Date('M d, Y',time()+3*24*3600);
          }
          $model->start_time = Yii::$app->request->post()['MeetingTime']['start_time'];
          $selected_time = date_parse($model->start_time);
          if ($selected_time['hour'] === false) {
            $selected_time['hour'] =9;
            $selected_time['minute'] =0;
          }
          // convert date time to timestamp
          $model->start = strtotime($model->start) +  $selected_time['hour']*3600+ $selected_time['minute']*60;
          $model->end = $model->start + (3600*$model->duration);
          // validate the form against model rules
          if ($model->validate()) {
              // all inputs are valid
              $model->save();
              Meeting::displayNotificationHint($meeting_id);
              return $this->redirect(['/meeting/view', 'id' => $model->meeting_id]);
          } else {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, this date time may be a duplicate or there is some other problem.'));
              $model->start = Date('M d, Y',time()+3*24*3600);
              $model->start_time = '9:00 am';
                // validation failed
              return $this->render('create', [
                  'model' => $model,
                'title' => $title,
              ]);
          }
        } else {
          $model->start = Date('M d, Y',strtotime('today midnight')+3600*24*3);
          $model->start_time = '';//Date('g:i a',time()+3*24*3600+9*60);

          return $this->render('create', [
              'model' => $model,
            'title' => $title,
          ]);
        }
    }

    /**
     * Updates an existing MeetingTime model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MeetingTime model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionChoose($id,$val) {
      // meeting_time_id needs to be set active
      // other meeting_time_id for this meeting need to be set inactive
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $meeting_id = intval($id);
      $mtg=Meeting::find()->where(['id'=>$meeting_id])->one();
      if (Yii::$app->user->getId()!=$mtg->owner_id &&
        !$mtg->meetingSettings['participant_choose_date_time']) return false;
      foreach ($mtg->meetingTimes as $mt) {
        if ($mt->id == intval($val)) {
          $mt->status = MeetingTime::STATUS_SELECTED;
          MeetingLog::add($meeting_id,MeetingLog::ACTION_CHOOSE_TIME,Yii::$app->user->getId(),intval($val));
        }
        else
          $mt->status = MeetingTime::STATUS_SUGGESTED;
        $mt->save();
      }
      return true;
    }

    public function actionRemove($id)
    {
      $result=MeetingTime::removeTime($id);
      // successful result returns $meeting_id to return to
      if ($result!==false) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','The meeting time option has been removed.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you may not have the right to remove meeting time options.'));
      }
      return $this->redirect(['/meeting/view','id'=>$result]);
    }


    /**
     * Finds the MeetingTime model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingTime the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingTime::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
