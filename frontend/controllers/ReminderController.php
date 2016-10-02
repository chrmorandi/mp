<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Reminder;
use frontend\models\MeetingReminder;
use frontend\models\ReminderSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReminderController implements the CRUD actions for Reminder model.
 */
class ReminderController extends Controller
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
                 ],
             ],
           'access' => [
                         'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
                         'rules' => [
                           // allow authenticated users
                            [
                                'allow' => true,
                                'actions'=>['index','view','create','update','delete'],
                                'roles' => ['@'],
                            ],
                           [
                               'allow' => true,
                               'actions'=>[''],
                               'roles' => ['?'],
                           ],
                           // everything else is denied
                         ],
                     ],
         ];
     }

    /**
     * Lists all Reminder models.
     * @return mixed
     */
    public function actionIndex()
    {      
      $remProvider = new ActiveDataProvider([
            'query' => Reminder::find()->joinWith('user')->where(['user_id'=>Yii::$app->user->getId()]),
            //'sort'=> ['defaultOrder' => ['name'=>SORT_ASC]],
        ]);

        $searchModel = new ReminderSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $remProvider,
        ]);
    }

    /**
     * Displays a single Reminder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Reminder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      if (!Reminder::withinLimit(Yii::$app->user->getId())) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of reminders. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/reminder/']);
      }

        $model = new Reminder();
        $model->user_id = Yii::$app->user->getId();
        $model->duration = 0;
        if ($model->load(Yii::$app->request->post())) {
          $model->duration = $model->setDuration($model->duration_friendly,$model->unit);
          // note - validation for integer isn't working here
          if ($model->reminder_type =='') {
            $model->reminder_type = Reminder::TYPE_EMAIL;
          }
          if ($model->validate()) {
            $model->save();
            Reminder::processNewReminder($model->id);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your reminder has been created for all current and future meetings.'));
            return $this->redirect('index');
          } else {
            // to do set flash
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','There was a problem creating your reminder.'));
          }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reminder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
          $model->duration = $model->setDuration($model->duration_friendly,$model->unit);
          if ($model->validate()) {
              $model->save();
              $model->updateReminder($id);
              Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your reminder has been updated for all current and future meetings.'));
            } else {
              // to do set flash
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','There was a problem creating your reminder.'));
            }
            return $this->redirect(['/reminder']);
          // update all the meeting reminders for this reminder
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Reminder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $r = Reminder::findOne($id);
        if ($r->user_id == Yii::$app->user->getId()) {
            $this->findModel($id)->delete();
            MeetingReminder::deleteAll(['reminder_id'=>$id]);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your reminder has been deleted.'));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Reminder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Reminder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reminder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
