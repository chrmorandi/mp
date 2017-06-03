<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use common\components\SiteHelper;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use frontend\models\UserProfile;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\Auth;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [''],
                'rules' => [
                    [
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => [''],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception instanceof \yii\web\NotFoundHttpException) {
            // all non existing controllers+actions will end up here
            return $this->render('pnf'); // page not found
        } else {
          return $this->render('error', ['exception' => $exception]);
        }
    }

    public function actionIndex()
    {
      if (Yii::$app->user->isGuest) {
        $urlPrefix = (isset(Yii::$app->params['urlPrefix'])? $urlPrefix = Yii::$app->params['urlPrefix'] : '');
        if (Yii::$app->params['site']['id'] == SiteHelper::SITE_FD) {
          $this->layout = 'home_fd';
          return $this->render('index_fd',[
            'urlPrefix'=>'fd',
          ]);
        } else {
          $this->layout = 'home';
          return $this->render('index',[
            'urlPrefix'=>$urlPrefix,
          ]);
        }

      } else {
        // user is logged in
          if (Yii::$app->params['site']['id'] == SiteHelper::SITE_FD) {
            $this->redirect('dating');
          } else {
            $this->redirect('meeting');
          }
      }
    }

    public function actionNeverland()
    {
      // for abusive scripts like wp-login probes
      Yii::$app->end();
    }

    public function actionOffline()
    {
      return $this->render('offline');
    }

    public function actionLogo()
    {
      return $this->render('logo');
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionTeam()
    {
        return $this->render('team');
    }

    public function actionPrivacy()
    {
        return $this->render('privacy');
    }

    public function actionTerms()
    {
        return $this->render('tos');
    }

    public function actionTos()
    {
        return $this->render('tos');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');
            return $this->redirect('login');
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionFeatures() {
      //if (Yii::$app->user->isGuest) {
        $urlPrefix = (isset(Yii::$app->params['urlPrefix'])? $urlPrefix = Yii::$app->params['urlPrefix'] : '');
      //}
        return $this->render('features',['urlPrefix'=>$urlPrefix]);
    }

    public function actionAuthfailure()
    {
        return $this->render('authfailure');
    }

    public function actionUnavailable()
    {
        return $this->render('unavailable');
    }

    public function onAuthSuccess($client)
      {
        // mode via login or signup
        $mode =  Yii::$app->getRequest()->getQueryParam('mode');
        $attributes = $client->getUserAttributes();
        $serviceId = $attributes['id'];
        $serviceProvider = $client->getId();
        $serviceTitle = $client->getTitle();
        $firstname ='';
        $lastname='';
        $fullname ='';
        switch ($serviceProvider) {
          case 'facebook':
            if (!array_key_exists('email',$attributes)) {
              $fb_app_str = Html::a(Yii::t('frontend','remove us from your Facebook apps'),'https://www.facebook.com/settings?tab=applications');
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, we do require your email address from Facebook. Either').' '.$fb_app_str.' '.Yii::t('frontend','and try again, or register using a different method below.'));
              return $this->goHome();
            }
            $username = $email = $attributes['email'];
            $fullname = $attributes['name'];
            break;
          case 'google':
            $email = $attributes['emails'][0]['value'];
            if (isset($attributes['displayName'])) {
                $fullname = $username = $attributes['displayName'];
            }
            if (isset($attributes['name']['familyName']) and isset($attributes['name']['givenName'])) {
              $lastname = $attributes['name']['familyName'];
              $firstname = $attributes['name']['givenName'];
            }
          break;
          case 'linkedin':
            $username = $email = $attributes['email-address'];
            $lastname = $attributes['first-name'];
            $firstname = $attributes['last-name'];
            $fullname = $firstname.' '.$lastname;
          break;
          /*case 'twitter':
            $username = $attributes['screen_name'];
            $fullname = $attributes['name'];
            // to do - fix social helpers
            $email = $serviceId.'@twitter.com';
          break;*/
        }
        // to do - split names into first and last with parser
        // lookup social auth result to see if we know it
        $auth = Auth::find()->where([
            'source' => (string)$serviceProvider,
            'source_id' => (string)$serviceId,
        ])->one();
        if (Yii::$app->user->isGuest) {
            // not logged in: either sign in or register
            if ($auth) {
              // if the user_id associated with this oauth login is registered, try to log them in
              $user_id = $auth->user_id;
              $person = new \common\models\User;
              $identity = $person->findIdentity($user_id);
              User::completeInitialize($user_id);
              UserProfile::applySocialNames($user_id,$firstname,$lastname,$fullname);
              Yii::$app->user->login($identity);
            } else {
                // auth_id is new to us
                $user = User::find()->where(['email' => $email])->one();
                // this check may override which mode they came from
                if (is_null($user)) {
                  // email is unregistered, sign them up
                  $mode='signup';
                } else {
                  // email exists, log them in
                  $mode ='login';
                }
                switch ($mode) {
                  case 'login':
                    // logging in but account not yet connected, may be passive
                    $auth = new Auth([
                        'user_id' => $user->id,
                        'source' => $serviceProvider, // $client->getId(),
                        'source_id' => (string)$serviceId, // (string)$attributes['id'],
                    ]);
                    if ($auth->save()) {
                        $user->status = User::STATUS_ACTIVE;
                        $user->update();
                        User::completeInitialize($user->id);
                        UserProfile::applySocialNames($user->id,$firstname,$lastname,$fullname);
                        Yii::$app->user->login($user);
                    } else {
                        print_r($auth->getErrors());
                    }
                    break;
                  case 'signup':
                    // sign up a new account using oauth
                    // look for username that exists already and differentiate it
                    if (isset($username) && User::find()->where(['username' => $username])->exists()) {
                      $username = User::generateUniqueUsername($username);
                    }
                    $password = Yii::$app->security->generateRandomString(12);
                      $user = new User([
                          'username' => $username, // $attributes['login'],
                          'email' => $email,
                          'password' => $password,
                          'status' => User::STATUS_ACTIVE,
                      ]);
                      $user->generateAuthKey();
                      $user->generatePasswordResetToken();
                      $transaction = $user->getDb()->beginTransaction();
                      if ($user->save()) {
                          $auth = new Auth([
                              'user_id' => $user->id,
                              'source' => $serviceProvider, // $client->getId(),
                              'source_id' => (string)$serviceId, // (string)$attributes['id'],
                          ]);
                          if ($auth->save()) {
                              User::completeInitialize($user->id);
                              UserProfile::applySocialNames($user->id,$firstname,$lastname,$fullname);
                              $transaction->commit();
                              Yii::$app->user->login($user);
                          } else {
                              print_r($auth->getErrors());
                          }
                      } else {
                          $transaction->rollBack();
                          print_r($user->getErrors());
                      }
                  break;
                  case 'schedule':
                  // to do - neeeds integration above as well
                  break;
                }
            }
        } else {
          // already signed in, just link the social account and make user active
          UserProfile::applySocialNames(Yii::$app->user->id,$firstname,$lastname,$fullname);
          // user already logged in, link the accounts
            if (!$auth) { // add auth provider
                $auth = new Auth([
                    'user_id' => Yii::$app->user->id,
                    'source' => $serviceProvider,
                    'source_id' => (string)$serviceId,
                ]);
                $auth->validate();
                $auth->save();
                $u = User::findOne(Yii::$app->user->id);
                $u->status = User::STATUS_ACTIVE;
                $u->update();
                Yii::$app->session->setFlash('success', Yii::t('frontend', 'Your {serviceProvider} account has been connected to your account. In the future you can log in with a single click of its logo.',
['serviceProvider'=>$serviceTitle]));
            }
        }
    }
}
