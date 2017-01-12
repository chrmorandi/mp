<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingLog;
use frontend\models\MeetingLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MeetingLogController implements the CRUD actions for MeetingLog model.
 */
class MeetingLogController extends Controller
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
           'access' => [
                         'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
                         'rules' => [
                           // allow authenticated users
                            [
                                'allow' => true,
                                'actions'=>['index','view'],
                                'roles' => ['@'],
                            ],
                           [
                               'allow' => true,
                               'actions'=>[],
                               'roles' => ['?'],
                           ],
                           // everything else is denied
                         ],
                     ],
         ];
     }

    /**
     * Lists all MeetingLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        // only administrators can view the meeting log
        if (!User::findOne(['id' => Yii::$app->user->getId()])->isAdmin()) {
            $this->redirect(['site/authfailure']);
        }
        $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
        $searchModel = new MeetingLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'timezone' => $timezone,
        ]);
    }

    /**
     * Displays a single MeetingLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      if (!Meeting::isAttendee($id,Yii::$app->user->getId())) {
        $this->redirect(['site/authfailure']);
      }
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      Yii::$app->timeZone = $timezone;
			$searchModel = new MeetingLogSearch();
      $dataProvider = $searchModel->search(['MeetingLogSearch'=>['meeting_id'=>$id]]);
      $m= Meeting::findOne($id);
      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
          'meeting_id' => $id,
          'subject' => $m->getMeetingHeader('log'),
          'timezone' => $timezone,
      ]);
    }

    /**
     * Finds the MeetingLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
