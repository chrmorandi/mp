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
use frontend\models\Auth;

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
                   'actions'=>['view','register','regtest','sigtest'],
                   'roles' => ['@'],
               ],
              [
                  'allow' => true,
                  'actions'=>['view','register','regtest','sigtest'],
                  'roles' => ['?'],
              ],
              // everything else is denied
            ],
        ],
      ];
  }

  /*public function beforeAction($action)
  {
    // your custom code here, if you want the code to run before action filters,
    // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
    if (!parent::beforeAction($action)) {
        return false;
    }
    $app_id = Yii::$app->getRequest()->getQueryParam('app_id');
    $app_secret = Yii::$app->getRequest()->getQueryParam('app_secret');
    if (Service::verifyAccess($app_id,$app_secret)) {
      return true;
    } else {
      echo 'your api keys are from the dark side';
      Yii::$app->end();
    }
  }*/

  public function actionIndex() {
    echo 'index';
    Yii::$app->end();
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

    public function actionSigtest($str='jeff@lookahead.iojeffreifman7799442211xyzfacebook') {
      $sig_target = hash_hmac('sha256',$str,Yii::$app->params['app_secret']);
      return $sig_target;
    }

    public function actionRegtest($app_id='',$email='',$firstname ='',$lastname='',$oauth_token='',$source='',$sig='') {
      // could move to before action by looping query params
      // concatenate string of arguments using alphabetical order of the variable namespace and leave out $app_id and $sig
      $sig_target = hash_hmac('sha256',$email.$firstname.$lastname.$oauth_token.$source,Yii::$app->params['app_secret']);
      if ($app_id == Yii::$app->params['app_id'] && $sig==$sig_target) {
        return 'it worked!';
      } else {
        return 'failed!';
      }
    }

    public function actionRegister($app_id='', $app_secret='', $source='',$firstname ='',$lastname='',$email = '',$oauth_token='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
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
      switch ($source) {
        case 'facebook':
        case 'google':
          // lookup oauth token
          $auth = Auth::find()->where([
              'source' => (string)$source,
              'source_id' => (string)$oauth_token,
          ])->one();
          if (is_null($auth)) {
            // store new oauth token
            $auth = new Auth([
                'user_id' => $user_id,
                'source' => $source,
                'source_id' => (string)$oauth_token,
            ]);
            $auth->save();
          }
          break;
        case 'manual':
          // do nothing
        break;
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
