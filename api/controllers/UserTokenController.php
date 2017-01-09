<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
namespace api\controllers;
use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\HeaderCollection;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\User;
use api\models\UserToken;
use api\models\Service;
use frontend\models\Auth;

/**
 * UserTokenController provides API functionality for registration, delete and verify
 *
 * @author Jeff Reifman <jeff@meetingplanner.io>
 * @since 0.1
 */
class UserTokenController extends Controller
{
  public $headers;

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

  /**
   * Called beforeAction for each public API method
   *  captures $this->header from http_get_request_headers
   *  checks if app_id is correct
   *
   * @param action $action the controller action
   * @return boolean true if app_id matches, false if it doesn't
   * @throws Exception not yet implemented
   */
  public function beforeAction($action)
  {
    // your custom code here, if you want the code to run before action filters,
    // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl
    if (!parent::beforeAction($action)) {
        return false;
    }
    $this->headers = Yii::$app->request->headers;
    if ($this->headers->has('app_id') && $this->headers->get('app_id')==Yii::$app->params['app_id']) {
      return true;
    } else {
      echo 'your api keys are from the dark side';
      Yii::$app->end();
    }
  }

  /**
   * Used for testing to generate a signature with app_secret from parameter string
   *
   * @param string $str string of combined parameters
   * @return string $sig_target hashed signature that should be used
   * @throws Exception not yet implemented
   */
  public function actionSigtest($str='tom@macfarlins.comthomasmacfarlinszuckerburger') {
    $sig_target = hash_hmac('sha256',$str,Yii::$app->params['app_secret']);
    return $sig_target;
  }

  /**
   * Verifies validity of a user token
   *
   * @param string $signature the hash signature for this request, uses app_secret
   * @param string $app_id in header, application id
   * @param integer $user_id in header, refers to owner of user_token to be validated
   * @param string $user_token in header, the token to be validated for this user_id
   * @return boolean true on success
   * @throws Exception not yet implemented
   */
    public function actionVerify($signature, $app_id='', $user_id=0,$user_token='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($this->headers->has('app_id')) {
        $app_id = $this->headers->get('app_id');
      }
      $user_id = $this->headers->get('user_id');
      $user_token = $this->headers->get('user_token');
      $sig_target = hash_hmac('sha256',$app_id.$user_id.$user_token);
      if ($signature==$sig_target) {
        $ut = UserToken::find()
          ->where(['user_id'=>$user_id])
          ->one();
          if (is_null($ut)) {
            return false;
          } else {
              return true;
          }
      } else {
        return false;
      }
    }

    /**
     * Register a new user with an external social Oauth_token
     *
     * @param string $signature the hash generated with app_secret
     * @param string $app_id in header, the shared secret application id
     * @param string $email in header, email address
     * @param string $firstname in header, first name
     * @param string $lastname in header, last name
     * @param string $oauth_token in header, the token returned from Facebook during OAuth for this user
     * @param string $source in header, the source that the $oauth_token is from e.g. 'facebook'
     * @return obj $identityObj with user_id and user_token
     * @throws Exception not yet implemented
     */
    public function actionRegister($signature,$app_id='',$email='',$firstname ='',$lastname='',$oauth_token='',$source='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $email= $this->headers->get('email');
      $firstname= $this->headers->get('firstname');
      $lastname= $this->headers->get('lastname');
      $oauth_token= $this->headers->get('oauth_token');
      $source = $this->headers->get('source');
      if ($this->headers->has('app_id')) {
        $app_id = $this->headers->get('app_id');
      }
      $sig_target = hash_hmac('sha256',$email.$firstname.$lastname.$oauth_token.$source,Yii::$app->params['app_secret']);
      if ($signature==$sig_target) {
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
        } else {
          return false;
        }
    }

    /**
     * Delete a user by user_id
     *
     * @param string $signature in header, the generated hash using the user's own token
     * @param string $app_id in header, the shared secret application id
     * @param string $user_id in header, of account
     *
     * @return boolean true if deleted, false if not
     * @throws Exception not yet implemented
     */
    public function actionDelete($signature='',$app_id='', $user_id =0) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $app_id= $this->headers->get('app_id');
      $user_id= $this->headers->get('user_id');
      $arg_str = $app_id.$user_id;
      if (Service::verifySignature($signature,$user_id,$arg_str)) {
        // mark user account as deleted
        $u=User::findOne($user_id);
        $u->status=User::STATUS_DELETED;
        $u->update();
        return true;
      } else {
          return false;
      }
    }

    /*
    public function actionRegtest($signature,$app_id='',$email='',$firstname ='',$lastname='',$oauth_token='',$source='') {
      // concatenate string of arguments using alphabetical order of the variable namespace and leave out $app_id and $sig
      $sig_target = hash_hmac('sha256',$email.$firstname.$lastname.$oauth_token.$source,Yii::$app->params['app_secret']);
      if ($app_id == Yii::$app->params['app_id'] && $sig==$sig_target) {
        return 'it worked!';
      } else {
        return 'failed!';
      }
    }

    public function actionAlamo($sig='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $headers = Yii::$app->request->headers;
      $email= $headers->get('email');
      return $email;
    }

    public function actionHeadertest($sig='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      // could move to before action by looping query params
      // concatenate string of arguments using alphabetical order of the variable namespace and leave out $app_id and $sig
      //$app_id='',$email='',$firstname ='',$lastname='',$oauth_token='',$source='',
      $headers = Yii::$app->request->headers;
      $email= $headers->get('email');
      $firstname= $headers->get('firstname');
      $lastname= $headers->get('lastname');
      $oauth_token= $headers->get('oauth_token');
      $source = $headers->get('source');
      if ($headers->has('app_id')) {
        $app_id = $headers->get('app_id');
      }
      $sig_target = hash_hmac('sha256',$email.$firstname.$lastname.$oauth_token.$source,Yii::$app->params['app_secret']);
      if ($app_id == Yii::$app->params['app_id'] && $sig==$sig_target) {
        return 'it worked!';
      } else {
        return 'failed!';
      }
    }

    //curl_setopt($ch, CURLOPT_POST, 1);
    //curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
    public function actionCurl($sig) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,"http://localhost:8888/mp-api/user-token/register?sig=".$sig);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $headers = [
        'app_id: '.'imwithher',
        'email: '.'tom@macfarlins.com',
        'firstname: '.'thomas',
        'lastname: '.'macfarlins',
        'oauth_token: '.'zuckerburger',
        'source: '.'facebook',
      ];
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      $server_output = curl_exec ($ch);
      var_dump($server_output);
      curl_close ($ch);
    }

    public function actionHeaderusertest($user_id=0,$sig='') {
      Yii::$app->response->format = Response::FORMAT_JSON;
      // could move to before action by looping query params
      // concatenate string of arguments using alphabetical order of the variable namespace and leave out $app_id and $sig
      //$app_id='',$email='',$firstname ='',$lastname='',$oauth_token='',$source='',
      $headers = Yii::$app->request->headers;
      $email= $headers->get('email');
      $firstname= $headers->get('firstname');
      $lastname= $headers->get('lastname');
      $oauth_token= $headers->get('oauth_token');
      $source = $headers->get('source');
      if ($headers->has('app_id')) {
        $app_id = $headers->get('app_id');
      }
      // fetch token using user_id into $user_token

      $sig_target = hash_hmac('sha256',$email.$firstname.$lastname.$oauth_token.$source,$user_token);
      if ($app_id == Yii::$app->params['app_id'] && $sig==$sig_target) {
        return 'it worked!';
      } else {
        return 'failed!';
      }
    }

    */
}
