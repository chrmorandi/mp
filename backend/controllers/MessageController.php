<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\models\Message;
use backend\models\MessageSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
          'access' => [
              'class' => AccessControl::className(),
              //'only' => ['index'],
              'rules' => [
                  [
                      'allow' => true,
                      'matchCallback' => function ($rule, $action) {
                          return (!\Yii::$app->user->isGuest && \common\models\User::findOne(Yii::$app->user->getId())->isAdmin());
                        }
                  ],
              ],
          ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
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
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionTrash($id)
    {
        $user_id = Yii::$app->user->getId();
        if ($this->findModel($id)->trash($user_id)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('backend','Your message has been deleted.'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('backend','Sorry, we had a problem deleting your message.'));
          }
        return $this->redirect(['index']);
    }

    public function actionTest($id)
    {
      // sends out a test of this message to the admin
      // to do
      $m = new Message();
      $m->test($id);
      Yii::$app->getSession()->setFlash('success', Yii::t('backend','A test version of this has been sent to administrators.'));
      return $this->redirect(['index']);
    }

    public function actionNext10($id)
    {
      // sends out message to all users not blocking updates and all email
      $m = new Message();
      $m->send($id,10);
        Yii::$app->getSession()->setFlash('success', Yii::t('backend','This message has been sent.'));
        return $this->redirect(['index']);
    }

    public function actionNext25($id)
    {
      // sends out message to all users not blocking updates and all email
      $m = new Message();
      $m->send($id,25);
        Yii::$app->getSession()->setFlash('success', Yii::t('backend','This message has been sent.'));
        return $this->redirect(['index']);
    }

    public function actionNext50($id)
    {
      // sends out message to all users not blocking updates and all email
      $m = new Message();
      $m->send($id,50);
        Yii::$app->getSession()->setFlash('success', Yii::t('backend','This message has been sent.'));
        return $this->redirect(['index']);
    }

    public function actionNext100($id)
    {
      // sends out message to all users not blocking updates and all email
      $m = new Message();
      $m->send($id,100);
        Yii::$app->getSession()->setFlash('success', Yii::t('backend','This message has been sent.'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
