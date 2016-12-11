<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use frontend\models\Ticket;
use frontend\models\TicketSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
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

    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
      if (Yii::$app->user->isGuest) {
        $session = Yii::$app->session;
        $session->open();
        if (!$session->has('guest_id')) {
          $session->set('guest_id', Yii::$app->security->generateRandomString(32));
        }
        $guest_id = $session->get('guest_id');

        echo '<br /><br /><br /><br /><br /><br />'.$guest_id;

      } else {
        $guest_id = 0;
      }
      //andWhere(['status'=>[Ticket::STATUS_PLANNING,Meeting::STATUS_SENT]])
        $ticketProvider = new ActiveDataProvider([
              'query' => Ticket::find()->where(['posted_by'=>Yii::$app->user->getId()])->orWhere(['posted_by'=>$guest_id]),
              'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
              'pagination' => [
                  'pageSize' => 7,
                ],
          ]);
        if ($ticketProvider->getTotalCount()==0) {
          return $this->redirect(['ticket/create']);
        }
        exit;
        return $this->render('index', [
            'ticketProvider' => $ticketProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
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
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ticket();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Ticket model.
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
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
