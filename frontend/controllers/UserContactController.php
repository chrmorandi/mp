<?php

namespace frontend\controllers;

use Yii;
use frontend\models\UserContact;
use frontend\models\UserContactSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserContactController implements the CRUD actions for UserContact model.
 */
class UserContactController extends Controller
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
                                'actions' => ['index','create','update','view','delete','verify'],
                                'roles' => ['@'],
                            ],
                            // everything else is denied
                        ],
                    ],

        ];
    }

    /**
     * Lists all UserContact models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserContactSearch();
		      $searchModel->user_id = Yii::$app->user->getId();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserContact model.
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
     * Creates a new UserContact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      if (!UserContact::withinLimit(Yii::$app->user->getId())) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of contacts. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/user-contact']);
      }
        $model = new UserContact();
		    if ($model->load(Yii::$app->request->post())) {
			    $form = Yii::$app->request->post();
            if (!is_numeric($model->contact_type)) {
               $model->contact_type=UserContact::TYPE_OTHER;
            }
            $model->user_id= Yii::$app->user->getId();
            // validate the form against model rules
            if ($model->validate()) {
                // all inputs are valid
                // to do - if saving with SMS on, then run clear others
                $model->save();
                return $this->redirect(['index']);
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
     * Updates an existing UserContact model.
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
     * Deletes an existing UserContact model.
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
     * Finds the UserContact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserContact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserContact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionVerify($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            // display verification form
            $model->verify = Yii::$app->request->post()['UserContact']['verify'];
            if ((string)$model->verify_code == (string)$model->verify) {
              $model->status = UserContact::STATUS_VERIFIED;
              $model->update();
              Yii::$app->getSession()->setFlash('success',Yii::t('frontend','Thank you, your number is confirmed.'));
              return $this->redirect(['/user-contact']);
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, that is incorrect. Please request a new code.'));
                return $this->redirect(['/user-contact']);
            }
        } else {
          $canRequest = $model->canRequest();
          if ($canRequest) {
            // send a text to this number
            $model->requestCode();
            return $this->render('verify', [
                'model' => $model,
            ]);
          } else {
            Yii::$app->getSession()->setFlash('error', $canRequest);
            return $this->redirect(['/user-contact']);
          }
        }
    }
}
