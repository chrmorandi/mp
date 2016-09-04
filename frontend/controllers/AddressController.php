<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Address;
use frontend\models\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AddressController implements the CRUD actions for Address model.
 */
class AddressController extends Controller
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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                  // allow authenticated users
                   [
                       'allow' => true,
                       'actions'=>['import'],
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
     * Lists all Address models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Address model.
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
     * Creates a new Address model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Address();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Address model.
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
     * Deletes an existing Address model.
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
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImport() {
      $address = new Address();
      $session = Yii::$app->session;
      if (isset($_GET['reset'])) {
        $session->remove('google_code');
        $this->redirect('/mp/address/import');
      }

      //$session->remove('google_code');
      //exit;
      $redirect_uri='http://localhost:8888/mp/address/import';
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
    		header('Location: http://localhost:8888/mp/address/import');
        exit;
    	} else {
        $session_code = $session->get('google_code');
        if (!isset($session_code)) {
          //Html::a($googleImportUrl,$googleImportUrl)
            $this->redirect( $googleImportUrl);
            //exit;
        }
      }

      //$client -> setRedirectUri('https://meetingplanner.io/address/import');

      if (isset($session_code)) {
        $auth_code = $session_code;
		      $max_results = 1000;
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
	    $result = $address->curl('https://accounts.google.com/o/oauth2/token',$post);
	    $response =  json_decode($result);
      if (isset($response->error)) {
          var_dump($response);
          exit;
      }
	    $accesstoken = $response->access_token;
	    $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&alt=json&v=3.0&oauth_token='.$accesstoken;
	    $xmlresponse =  $address->curl($url);
	    $contacts = json_decode($xmlresponse,true);
		    //var_dump ( $contacts);
		      $return = array();
  		if (!empty($contacts['feed']['entry'])) {
  			foreach($contacts['feed']['entry'] as $contact) {
          if (isset($contact['gd$email'])) {
            //var_dump($contact);
            $temp = array (
              'firstname' => (isset($contact['gd$name']['gd$givenName']['$t'])?$contact['gd$name']['gd$givenName']['$t']:''),
              'lastname' => (isset($contact['gd$name']['gd$familyName']['$t'])?$contact['gd$name']['gd$familyName']['$t']:''),
    					'fullname'=> $contact['title']['$t'],
    					'email' => $contact['gd$email'][0]['address'],
    				);
            $return[]=$temp;
            $address->add($temp);
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


}
