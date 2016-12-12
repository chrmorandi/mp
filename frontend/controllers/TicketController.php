<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use frontend\models\Ticket;
use frontend\models\TicketReply;
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
      if (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()) {
        // admin
        $guest_id = Yii::$app->user->getId();
        $query = Ticket::find()
          ->where(['status'=>[Ticket::STATUS_OPEN,Ticket::STATUS_PENDING]]);
      } else {
        // user
        $guest_id = Ticket::getGuestId();
        $query = Ticket::find()
          ->where(['posted_by'=>Yii::$app->user->getId()])
          ->orWhere(['posted_by'=>$guest_id])
          ->andWhere(['status'=>[Ticket::STATUS_OPEN,Ticket::STATUS_PENDING]]);
      }
        $ticketProvider = new ActiveDataProvider([
              'query' => $query,
              'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
              'pagination' => [
                  'pageSize' => 7,
                ],
          ]);
        if ($ticketProvider->getTotalCount()==0) {
          return $this->redirect(['ticket/create']);
        } else {
          return $this->render('index', [
              'ticketProvider' => $ticketProvider,
          ]);
        }
    }

    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      if (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin()) {
        $mode = 'admin';
      } else {
        $mode = 'user';
      }
      $model = $this->findModel($id);
      $reply = new TicketReply();
      if ($reply->load(Yii::$app->request->post())) {
          $reply->ticket_id = $id;
          if ($mode=='admin') {
            $model->status = Ticket::STATUS_PENDING_USER;
            $reply->posted_by = Yii::$app->user->getId();
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve notified the user of your update.');
          } else {
            $model->status = Ticket::STATUS_PENDING;
            $reply->posted_by = Ticket::getGuestId();
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve notified our staff of your update. We\'ll get back to you as soon as possible.');
          }
          $reply->save();
          $model->update();
          return $this->redirect(['view','id'=>$id]);
      } else {
        return $this->render('view', [
            'model' => $model,
            'reply' => $reply,
        ]);
      }
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ticket();
        if ($model->load(Yii::$app->request->post())) {
            $model->posted_by = Ticket::getGuestId();
            $model->status = Ticket::STATUS_OPEN;
            $model->save();
            Yii::$app->session->setFlash('success', 'Thank you, we\'ve created a new ticket and notified our staff. We\'ll get back to you as soon as possible.');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionClose($id)
    {
        $t = Ticket::findOne($id);
        $t->status = Ticket::STATUS_CLOSED;
        $t->update();
        Yii::$app->session->setFlash('success', 'Thank you, we\'ve closed this ticket. Let us know when we can help you again.');
        return $this->redirect(['index']);
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
