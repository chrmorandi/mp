<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use frontend\models\Meeting;
use frontend\models\MeetingSearch;
use frontend\models\Participant;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
use frontend\models\MeetingSetting;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MeetingController implements the CRUD actions for Meeting model.
 */
class MeetingController extends Controller
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
/*           'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'only' => ['index','view','create','update','delete', 'cancel'],
                        'rules' => [
                            // everything else is denied
                        ],
                    ],
                    */
        ];
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionIndex()
    {
      // add filter for upcoming or past
      $upcomingProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT,Meeting::STATUS_CONFIRMED]]),
        ]);

        $pastProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>Meeting::STATUS_COMPLETED]),
        ]);
        $canceledProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>Meeting::STATUS_CANCELED]),
        ]);

        return $this->render('index', [
            'upcomingProvider' => $upcomingProvider,
            'pastProvider' => $pastProvider,
            'canceledProvider' => $canceledProvider,
        ]);
    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      $timeProvider = new ActiveDataProvider([
          'query' => MeetingTime::find()->where(['meeting_id'=>$id]),
      ]);

      $noteProvider = new ActiveDataProvider([
          'query' => MeetingNote::find()->where(['meeting_id'=>$id]),
      ]);

      $placeProvider = new ActiveDataProvider([
          'query' => MeetingPlace::find()->where(['meeting_id'=>$id]),
      ]);

      $participantProvider = new ActiveDataProvider([
          'query' => Participant::find()->where(['meeting_id'=>$id]),
      ]);
      $model = $this->findModel($id);
      $model->prepareView();
        return $this->render('view', [
            'model' => $model,
            'participantProvider' => $participantProvider,
            'timeProvider' => $timeProvider,
            'noteProvider' => $noteProvider,
            'placeProvider' => $placeProvider,
            'viewer' => Yii::$app->user->getId(),
            'isOwner' => $model->isOwner(Yii::$app->user->getId()),
        ]);
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Meeting();
        if ($model->load(Yii::$app->request->post())) {
          $model->owner_id= Yii::$app->user->getId();
          // validate the form against model rules
          if ($model->validate()) {
              // all inputs are valid
              $model->save();
              $model->initializeMeetingSetting($model->id,$model->owner_id);
              return $this->redirect(['view', 'id' => $model->id]);
          } else {
              // validation failed
              return $this->render('create', [
                  'model' => $model,
              ]);
          }          
        } else {
          return $this->render('create', [
              'model' => $model,
          ]);          
        }
    }

    /**
     * Updates an existing Meeting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->title = $model->getMeetingTitle($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Meeting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    public function actionCancel($id) {
      $this->findModel($id)->cancel();
      return $this->redirect(['index']);
    }

    public function actionCansend($id,$viewer_id) {
      // ajax checks if viewer can send this meeting
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->findModel($id)->canSend($viewer_id);
    }

    public function actionCanfinalize($id,$viewer_id) {
      // ajax checks if viewer can send this meeting
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->findModel($id)->canFinalize($viewer_id);
    }
    
    public function actionSend($id) {
      $meeting = $this->findModel($id);
      if ($meeting->canSend(Yii::$app->user->getId())) {
        $meeting->send(Yii::$app->user->getId());
        Yii::$app->getSession()->setFlash('success', 'Your meeting invitation has been sent.');
        return $this->redirect(['index']);        
      } else {
        // failed
        Yii::$app->getSession()->setFlash('error', 'Sorry, your meeting invitation is not ready to send.');
        return $this->redirect(['view', 'id' => $id]);
      }
    }

    public function actionFinalize($id) {
      $meeting = $this->findModel($id);
      if ($meeting->canFinalize(Yii::$app->user->getId())) {
        $meeting->finalize(Yii::$app->user->getId());
        Yii::$app->getSession()->setFlash('success', 'Your meeting has been finalized.');
        return $this->redirect(['index']);        
      } else {
        // failed
        Yii::$app->getSession()->setFlash('error', 'Sorry, your meeting invitation cannot be finalized yet.');
        return $this->redirect(['view', 'id' => $id]);
      }
    }
    
    /**
     * Finds the Meeting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meeting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meeting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
}
