<?php

namespace frontend\controllers;

use Yii;
use common\models\User;
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
				if (!User::find(Yii::$app->user->getId())->one()->isAdmin()) {
					$this->redirect(['site/authfailure']);
				}
        $searchModel = new MeetingLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MeetingLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
			if (!User::find(Yii::$app->user->getId())->one()->isAdmin()) {
				$this->redirect(['site/authfailure']);
			}
      $searchModel = new MeetingLogSearch();
      $dataProvider = $searchModel->search(['MeetingLogSearch'=>['meeting_id'=>$id]]);
      return $this->render('index', [
          'searchModel' => $searchModel,
          'dataProvider' => $dataProvider,
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
