<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Friend;
use frontend\models\FriendSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\bootstrap\ActiveForm;
use yii\web\Request;
use yii\web\Response;
use yii\helpers\Html;

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
        ];
    }

    /**
     * Lists all Friend models.
     * @return mixed
     */
    public function actionIndex()
    {
      $friendProvider = new ActiveDataProvider([
            // to do - sort this by best display name
            //'sort'=> ['defaultOrder' => ['email?'=>SORT_DESC]],
            'query' => Friend::find()->joinWith('user')->where(['user_id'=>Yii::$app->user->getId()]),
            'pagination' => [
                'pageSize' => 10,
              ],
        ]);

        $searchModel = new FriendSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $friendProvider,
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
          // get user_id of email
          $user_id = $model->lookupEmail($model->email);
          if ($user_id===false) {
            $user_id = $model->addUser($model->email);
          }
          $model->friend_id = $user_id;
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

    public function actionGrab() {
      }

    public function actionImport() {
      $session = Yii::$app->session;
      if (isset($_GET['reset'])) {
        $session->remove('google_code');
        $this->redirect('/mp/friend/import');
      }

      //$session->remove('google_code');
      //exit;
      $redirect_uri='http://localhost:8888/mp/friend/import';
      $session->open();
      $client = new \Google_Client();
      $client -> setApplicationName('Meeting Planner');
      $client -> setClientid( Yii::$app->components['authClientCollection']['clients']['google']['clientId']);
      $client -> setClientSecret(Yii::$app->components['authClientCollection']['clients']['google']['clientSecret']);
      $client -> setRedirectUri($redirect_uri);
      $client -> setAccessType('online');
      $client -> setScopes('https://www.google.com/m8/feeds');
      $googleImportUrl = $client -> createAuthUrl();

      // moves returned code to session variables and returns here
      if (isset($_GET['code']))
    	{
    		$auth_code = $_GET['code'];
        $session->set('google_code',$auth_code);
    		header('Location: http://localhost:8888/mp/friend/import');
        exit;
    	} else {
        $session_code = $session->get('google_code');
        if (!isset($session_code)) {
            echo Html::a($googleImportUrl,$googleImportUrl);
            exit;
        }
      }

      //$client -> setRedirectUri('https://meetingplanner.io/friend/import');

      if (isset($session_code)) {
        $auth_code = $session_code;
		      $max_results = 5000;
	         $fields=array(
	        'code'=>  urlencode($auth_code),
	        'client_id'=>  urlencode(Yii::$app->components['authClientCollection']['clients']['google']['clientId']),
	        'client_secret'=>  urlencode(Yii::$app->components['authClientCollection']['clients']['google']['clientSecret']),
	        'redirect_uri'=>  urlencode($redirect_uri),
	        'grant_type'=>  urlencode('authorization_code'),
	    );
	    $post = '';
	    foreach($fields as $key=>$value)
	    {
	        $post .= $key.'='.$value.'&';
	    }
	    $post = rtrim($post,'&');
	    $result = $this->curl('https://accounts.google.com/o/oauth2/token',$post);
	    $response =  json_decode($result);
	    //var_dump($response);
	    $accesstoken = $response->access_token;
	    $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&alt=json&v=3.0&oauth_token='.$accesstoken;
	    $xmlresponse =  $this->curl($url);
	    $contacts = json_decode($xmlresponse,true);
		    //var_dump ( $contacts);
		      $return = array();
  		if (!empty($contacts['feed']['entry'])) {
  			foreach($contacts['feed']['entry'] as $contact) {
          if (isset($contact['gd$email'])) {
            $return[] = array (
    					'name'=> $contact['title']['$t'],
    					'email' => $contact['gd$email'][0]['address'],
    				);
          } else {
          continue;
          }
  			}
		  }
		$google_contacts = $return;
    var_dump($google_contacts);
		$session->remove('google_code');
	}
}

    public static function curl($url, $post = "") {
       $curl = curl_init();
       $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
       curl_setopt($curl, CURLOPT_URL, $url);
       //The URL to fetch. This can also be set when initializing a session with curl_init().
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
       //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
       curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
       //The number of seconds to wait while trying to connect.
       if ($post != "") {
       curl_setopt($curl, CURLOPT_POST, 5);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
       }
       curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
       //The contents of the "User-Agent: " header to be used in a HTTP request.
       curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
       //To follow any "Location: " header that the server sends as part of the HTTP header.
       curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
       //To automatically set the Referer: field in requests where it follows a Location: redirect.
       curl_setopt($curl, CURLOPT_TIMEOUT, 10);
       //The maximum number of seconds to allow cURL functions to execute.
       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
       //To stop cURL from verifying the peer's certificate.
       $contents = curl_exec($curl);
       curl_close($curl);
       return $contents;
     }
}
