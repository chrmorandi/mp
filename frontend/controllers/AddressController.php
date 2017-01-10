<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
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
                       'actions'=>['import','delete'],
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
        return $this->redirect(['/friend/index','tab'=>'address']);
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
      // imports contacts from the google api
      $address = new Address();
      // create session cookies
      $session = Yii::$app->session;
      // if we request code reset, then remove the google_code cookie
      // i.e. if google_code expires
      if (isset($_GET['reset']) && !$session->has('google_code_reset')) {
        // prevent loops
        $session->set('google_code_reset');
        // reset the google_code
        $session->remove('google_code');
        $this->redirect(['import']);
      }
      // always remove the reset request cookie
      $session->remove('google_code_reset');
      // build the API request
      $redirect_uri=Url::home(true).'address/import';
      $session->open();
      $client = new \Google_Client();
      $client -> setApplicationName('Meeting Planner');
      $client -> setClientId( Yii::$app->components['authClientCollection']['clients']['google']['clientId']);
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
    		header('Location: '.Url::home(true).'address/import');
        // do not remove - breaks the API
        exit; // do not replace with yii app end
        // do not remove above exit
    	} else {
        $session_code = $session->get('google_code');
        if (!isset($session_code)) {
            $this->redirect( $googleImportUrl);
        }
      }
      // Requests the user authentication
      if (isset($session_code)) {
        $auth_code = $session_code;
	      $fields= [
	        'code'=>  urlencode($auth_code),
	        'client_id'=>  urlencode(Yii::$app->components['authClientCollection']['clients']['google']['clientId']),
	        'client_secret'=>  urlencode(Yii::$app->components['authClientCollection']['clients']['google']['clientSecret']),
	        'redirect_uri'=>  urlencode($redirect_uri),
	        'grant_type'=>  urlencode('authorization_code'),
          ];
      // Requests the access token
	    $post = '';
	    foreach($fields as $key=>$value)
	    {
	        $post .= $key.'='.$value.'&';
	    }
	    $post = rtrim($post,'&');
	    $result = $address->curl('https://accounts.google.com/o/oauth2/token',$post);
	    $response =  json_decode($result);
      if (isset($response->error)) {
        // to do - remove this
          if ($response->error_description == 'Code was already redeemed.') {
            $session->remove('google_code');
            return $this->redirect(['import']);
          }
          if ($response->error_description == 'Invalid code.') {
            $session->remove('google_code');
            return $this->redirect(['import']);
          }
          var_dump($response);
          echo Yii::t('frontend','There was an error. Please contact support.');
      }
      if (isset($response->access_token) || empty($response->access_token)) {
          $accesstoken = $response->access_token;
      } else {
        echo Yii::t('frontend','There was an error. No access token. Please contact support.');
      }
      // Requests the data
      $startIndex = 1;
      $request_data = true;
      $max_results = Address::CONTACTS_PAGE_SIZE;
      $numberPages = 0;
      while ($request_data && $numberPages <5) {
         //echo 'calling with startIndex: '.$startIndex.'<br />';
         $url = 'https://www.google.com/m8/feeds/contacts/default/full?max-results='.$max_results.'&start-index='.$startIndex.'&alt=json&v=3.0&oauth_token='.$accesstoken;
         $xmlresponse =  $address->curl($url);
         $contacts = json_decode($xmlresponse,true);
         if (!isset($contacts['feed']['entry'])) {
           //var_dump ($url);
           //var_dump ($xmlresponse);
           exit;
         }
        $resultsCount =count($contacts['feed']['entry']);
        //echo 'count: '.$resultsCount.'<br />';
         //var_dump (count($contacts['feed']['entry']));
         // process out contacts without email adddresses
         //$return = array();
         if ($resultsCount>0) {
           foreach($contacts['feed']['entry'] as $contact) {
             if (isset($contact['gd$email'])) {
               $temp = [
                 'firstname' => (isset($contact['gd$name']['gd$givenName']['$t'])?$contact['gd$name']['gd$givenName']['$t']:''),
                 'lastname' => (isset($contact['gd$name']['gd$familyName']['$t'])?$contact['gd$name']['gd$familyName']['$t']:''),
                 'fullname'=> $contact['title']['$t'],
                 'email' => $contact['gd$email'][0]['address'],
               ];
               //$return[]=$temp;
               $address->add($temp);
             } else {
               continue;
             }
           }
           if ($resultsCount<$max_results) {
             Yii::$app->getSession()->setFlash('success', Yii::t('backend','Your contacts have been imported.'));
             return $this->redirect(['/friend','tab'=>'address']);
           }
         }
         //var_dump($return);
         $numberPages++;
         $startIndex+=$max_results;
       }
       $session->remove('google_code');
	 }
  }
}
