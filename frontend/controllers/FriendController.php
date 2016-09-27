<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\bootstrap\ActiveForm;
use yii\web\Request;
use yii\web\Response;
use yii\helpers\Html;
use common\models\User;
use frontend\models\Friend;
use frontend\models\Address;
use frontend\models\FriendSearch;
use frontend\models\AddressSearch;


/**
 * FriendController implements the CRUD actions for Friend model.
 */
class FriendController extends Controller
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
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                  // allow authenticated users
                   [
                       'allow' => true,
                       'actions'=>['create','index','view','update','delete'],
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
     * Lists all Friend models.
     * @return mixed
     */
    public function actionIndex()
    {
        $tab ='friend';
        if (!isset(Yii::$app->request->queryParams['tab']) && isset(Yii::$app->request->queryParams['AddressSearch'])) {
            $tab = 'address';
            }
        if (isset(Yii::$app->request->queryParams['tab'])) {
          $tab =Yii::$app->request->queryParams['tab'];
        }
        $friendSearchModel = new FriendSearch();
        $friendProvider = $friendSearchModel->search(Yii::$app->request->queryParams);

        $addressSearchModel = new AddressSearch();
        $addressProvider = $addressSearchModel->search(Yii::$app->request->queryParams);
        //$addressProvider->pagination->pageSize=20;
        return $this->render('index', [
            'friendProvider' => $friendProvider,
            'addressProvider' => $addressProvider,
            'addressSearchModel' => $addressSearchModel,
            'friendSearchModel'=>$friendSearchModel,
            'tab'=>$tab,
        ]);
    }

    /**
     * Displays a single Friend model.
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
     * Creates a new Friend model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      if (!Friend::withinLimit(Yii::$app->user->getId())) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, there are limits on how quickly you can add friends one by one. Try importing via Gmail, or visit support if you need assistance.'));
        return $this->redirect(['index']);
      }

        $model = new Friend();
        $model->user_id = Yii::$app->user->getId();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
          $model->friend_id = User::addUserFromEmail($model->email);
          // validate the form against model rules
          if ($model->validate()) {
              // all inputs are valid
              $model->save();
              return $this->redirect('index');
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
     * Updates an existing Friend model.
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
     * Deletes an existing Friend model.
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
     * Finds the Friend model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Friend the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Friend::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
