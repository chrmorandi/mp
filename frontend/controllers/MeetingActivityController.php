<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use yii\helpers\HTML;
//use yii\web\Response;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingActivity;
use frontend\models\MeetingLog;

/**
 * MeetingActivityController implements the CRUD actions for MeetingActivity model.
 */
class MeetingActivityController extends Controller
{
    const STATUS_SUGGESTED = 0;
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
                                'actions' => ['create','update','delete','choose','view','remove','add','insertactivity','loadchoices'],
                                'roles' => ['@'],
                            ],
                            // everything else is denied
                        ],
                    ],
        ];
    }

    /**
     * Displays a single MeetingActivity model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $timezone = MiscHelpers::fetchUserActivityzone(Yii::$app->user->getId());
        return $this->render('view', [
            'model' => $this->findModel($id),
            'timezone'=>$timezone,
        ]);
    }

    /**
     * Creates a new MeetingActivity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($meeting_id)
    {
      if (!MeetingActivity::withinLimit($meeting_id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of date times per meeting. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
      }
      Yii::$app->response->format = Response::FORMAT_JSON;
      $mtg = new Meeting();
      $title = $mtg->getMeetingTitle($meeting_id);
      $model = new MeetingActivity();
      $model->meeting_id= $meeting_id;
      $model->suggested_by= Yii::$app->user->getId();
      $model->status = self::STATUS_SUGGESTED;
        if ($model->load(Yii::$app->request->post())) {
          // validate the form against model rules
          if ($model->validate()) {
              // all inputs are valid
              $model->save();
              Meeting::displayNotificationHint($meeting_id);
              return $this->redirect(['/meeting/view', 'id' => $model->meeting_id]);
          } else {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, this activity may be a duplicate or there is some other problem.'));
                // validation failed
              return $this->render('create', [
                  'model' => $model,
                'title' => $title,
              ]);
          }
        } else {
          return $this->render('create', [
              'model' => $model,
            'title' => $title,
          ]);
        }
    }

    /**
     * Updates an existing MeetingActivity model.
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
     * Deletes an existing MeetingActivity model.
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
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $parts = explode('_', $val);
      // relies on naming of mp button id
      $ma_id = (int)$parts[2]; // get # from mp_plc_#
      $meeting_id = (int)$id;
      $mtg=Meeting::find()->where(['id'=>$meeting_id])->one();
      if (Yii::$app->user->getId()!=$mtg->owner_id &&
        !$mtg->meetingSettings['participant_choose_activity']) return false;
      foreach ($mtg->meetingActivities as $ma) {
        if ($ma->id == $ma_id) {
          $ma->status = MeetingActivity::STATUS_SELECTED;
          MeetingLog::add($meeting_id,MeetingLog::ACTION_CHOOSE_ACTIVITY,Yii::$app->user->getId(),$ma_id);
        }
        else {
          if ($ma->status == MeetingActivity::STATUS_SELECTED) {
              $ma->status = MeetingActivity::STATUS_SUGGESTED;
          }
        }
        $ma->save();
      }
      return true;
    }

    public function actionRemove($meeting_id,$activity_id)
    {
      $result=MeetingActivity::removeActivity($meeting_id,$activity_id);
      // successful result returns $meeting_id to return to
      if ($result!==false) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','The meeting activity option has been removed.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you may not have the right to remove meeting time options.'));
      }
      return $this->redirect(['/meeting/view','id'=>$meeting_id]);
    }

    public function actionAdd($id,$activity) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if (!MeetingActivity::withinLimit($id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of activities per meeting. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/meeting/view', 'id' => $id]);
      }
      $activity_str = urldecode($activity);
      $chk=MeetingActivity::find()
        ->where(['meeting_id'=>$id])
        ->andWhere(['activity'=>$activity_str])
        ->count();
      if ($chk==0) {
        $model = new MeetingActivity;
        $model->activity=$activity_str;
        $model->meeting_id = $id;
        $model->status=MeetingActivity::STATUS_SUGGESTED;
        $model->suggested_by = Yii::$app->user->getId();
        $model->save();
        return true;
      } else {
        return false;
      }
    }

    public function actionInsertactivity($id) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $meeting_id = $id;
      if (!Meeting::isAttendee($id,Yii::$app->user->getId())) {
        return false;
      }
      $model=Meeting::findOne($id);
      $activityProvider = new ActiveDataProvider([
          'query' => MeetingActivity::find()->where(['meeting_id'=>$id])
            ->andWhere(['status'=>[MeetingActivity::STATUS_SUGGESTED,MeetingActivity::STATUS_SELECTED]]),
          'sort' => [
            'defaultOrder' => [
              'availability'=>SORT_DESC
            ]
          ],
      ]);
      $activityStatus = MeetingActivity::getActivityStatus($model,Yii::$app->user->getId());
      $result = ListView::widget([
             'dataProvider' => $activityProvider,
             'itemOptions' => ['class' => 'item'],
             'layout' => '{items}',
             'itemView' => '/meeting-activity/_list',
             'viewParams' => ['activityCount'=>$activityProvider->count,
             'isOwner'=>$model->isOwner(Yii::$app->user->getId()),
             'participant_choose_activity'=>$model->meetingSettings['participant_choose_activity'],
             'activityStatus'=>$activityStatus],
         ]) ;
         return $result;
    }


    public function actionLoadchoices($id) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $model=Meeting::findOne($id);
      $activityProvider = new ActiveDataProvider([
          'query' => MeetingActivity::find()->where(['meeting_id'=>$id])
            ->andWhere(['status'=>[MeetingActivity::STATUS_SUGGESTED,MeetingActivity::STATUS_SELECTED]]),
          'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
      ]);
      if ($activityProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_activity'])) {
        return $this->renderPartial('_choices', [
              'model'=>$model,
          ]);
      } else {
        return false;
      }
    }
    /**
     * Finds the MeetingActivity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingActivity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingActivity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
