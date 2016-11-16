<?php
namespace api\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
//use yii\rest\ActiveController;
use common\models\User;
use api\models\UserToken;
use api\models\Service;

class UserTokenController extends Controller // ActiveController
{

  public function behaviors()
  {
      return [
        'access' => [
            'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
            'rules' => [
              // allow authenticated users
               [
                   'allow' => true,
                   'actions'=>['view','register'],
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

    public function actionVerify($app_id='', $app_secret='', $token='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $ut = UserToken::find()
        ->where(['user_id'=>$u->id])
        ->one();
        if (is_null($ut)) {
          // error
          return false;
        }
      return true;
    }

    public function actionRegister($app_id='', $app_secret='', $source='',$firstname ='',$lastname='',$email = '',$token='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      echo 'here';
      exit;
      // verify app_id and app_key
      if (!Service::verifyAccess($app_id,$app_secret)) {
        // to do - error msg
        return false;
      }
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
        $new_user_id = UserToken::signupUser($email, $firstname,$lastname);
        $user_id = $identityObj->user_id = $new_user_id;
      } else {
        // email already registered
        $user_id = $identityObj->user_id = $u->id;
      }
      // check if user_id already has a mobile token
      $ut = UserToken::find()
        ->where(['user_id'=>$user_id])
        ->one();
        if (is_null($ut)) {
          // create a token
          $ut = new UserToken();
          $ut->token = $identityObj->token = Yii::$app->security->generateRandomString(40);
          $ut->user_id = $user_id;
          $ut->save();
        } else {
          $identityObj->token = $ut->token;
        }
        return $identityObj;
    }

    public function actionDelete($app_id='', $app_secret='', $token='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if (!Service::verifyAccess($app_id,$app_secret)) {
        // to do - error msg
        return false;
      }

      $ut = UserToken::find()
        ->where(['user_id'=>$u->id])
        ->one();
        if (is_null($ut)) {
          // error
          return false;
        }
        // mark user account as deleted
      return true;
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
