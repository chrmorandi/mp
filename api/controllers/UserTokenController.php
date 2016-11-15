<?php
namespace api\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\User;
//use yii\rest\ActiveController;
use api\models\UserToken;

class UserTokenController extends Controller // ActiveController
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
                      'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
                      'rules' => [
                        // allow authenticated users
                         [
                             'allow' => true,
                             'actions'=>['view'],
                             'roles' => ['@'],
                         ],
                        [
                            'allow' => true,
                            'actions'=>['view','register'],
                            'roles' => ['?'],
                        ],
                        // everything else is denied
                      ],
                  ],
      ];
  }

    public function actionRegister($app_id='', $app_key='', $source='',$email = '',$token='') {
      // verify app_id and app_key
      Yii::$app->response->format = Response::FORMAT_JSON;
      $identityObj = new \stdClass();
      // $source = facebook, google, manual (user types it)
      // $email = the email from fb, google, manual (ideally always provided)
      // $token = the oauth token from fb or google when available
      // mobile token below is created by meeting planner to authenticate mobile users
      // mobile token is stored in user_token table
      // check email validity
      // check if email already registered
      $u = User::find()
        ->where(['email'=>$email])
        ->one();
      if (is_null($u)) {
        // email not yet registered in our system
        // register the user
        $identityObj->user_id = $new_user_id;
        // register a user token
        $identityObj->token = $new_token;
      } else {
        // email already registered
        $identityObj->user_id = $u->id;
        // check if user_id already has a mobile token
        $ut = UserToken::find()
          ->where(['user_id'=>$u->id])
          ->one();
          if (is_null($ut)) {
            // create a token
            $identityObj->token = $new_token;
          } else {
            $identityObj->token = $ut->token;
          }
          return $identityObj;
      }
    }

    public function actionIndex()
    {
      exit;
        $dataProvider = new ActiveDataProvider([
            'query' => UserToken::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Launch model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $ut = UserToken::findOne($id);
      return $ut;
    }

    /**
     * Creates a new Launch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Launch();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Launch model.
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
     * Deletes an existing Launch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        echo 'here';exit;
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Launch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Launch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Launch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
