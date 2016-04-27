<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\Auth;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
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
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup','error','authfailure'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','error','authfailure'],
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

    public function actionIndex()
    {
      if (Yii::$app->user->isGuest) {
          return $this->render('index');
      } else {
        // user is logged in
        $this->redirect('meeting/index');
      }


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

    public function actionPrivacy()
    {
        return $this->render('privacy');
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

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionAuthfailure()
    {
        return $this->render('authfailure');
    }

    public function actionUnavailable()
    {
        return $this->render('unavailable');
    }

    public function actionError()
    {
        return $this->render('error');
    }

    public function onAuthSuccess($client)
        {
          $mode =  Yii::$app->getRequest()->getQueryParam('mode');
          $attributes = $client->getUserAttributes();
          $serviceId = $attributes['id'];
          $serviceProvider = $client->getId();
          $serviceTitle = $client->getTitle();
          switch ($serviceProvider) {
            case 'facebook':
              $email = $attributes['email'];
              $username = $email;
              break;
            case 'google':
              $email = $attributes['emails'][0]['value'];
              $username = $attributes['displayName'];
            break;
            case 'twitter':
              // temp placeholder for email
              // to do : do not allow meeting creation without email
              //$email = $serviceId.'@twitter.com';
var_dump($attributes);exit;
              $email = $attributes['email'];
              $username = $attributes['screen_name'];
              //echo $email;

            break;
          }
            $auth = Auth::find()->where([
                'source' => $serviceProvider,
                'source_id' => $serviceId,
            ])->one();
            if (Yii::$app->user->isGuest) {
                if ($auth) { // login
                  $user_id = $auth->user_id;
                  $person = new \common\models\User;
                  $identity = $person->findIdentity($user_id);
                  Yii::$app->user->login($identity);
                } else { // signup
                  if ($mode == 'login') {
                    Yii::$app->getSession()->setFlash('error', [
                        Yii::t('frontend', "We don't recognize the user with this email from {client}. You can sign up with this account by trying again from the signup page below. ", ['client' => $serviceTitle]),
                    ]);
                    $this->redirect(['signup']);
                  } else if (isset($email) && isset($username) && User::find()->where(['email' => $email])->exists()) {
                        Yii::$app->getSession()->setFlash('error', [
                            Yii::t('frontend', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $serviceTitle]),
                        ]);
                    } else {
                      // important to do - look for username that exists already
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
                                'source_id' => $serviceId, // (string)$attributes['id'],
                            ]);
                            if ($auth->save()) {
                                $transaction->commit();
                                Yii::$app->user->login($user);
                            } else {
                                print_r($auth->getErrors());
                            }
                        } else {
                            print_r($user->getErrors());
                        }
                    }
                }
            } else { // user already logged in
                if (!$auth) { // add auth provider
                    $auth = new Auth([
                        'user_id' => Yii::$app->user->id,
                        'source' => $serviceProvider,
                        'source_id' => $serviceId,
                    ]);
                    $auth->validate();
                    $auth->save();
                    $u = User::findOne(Yii::$app->user->id);
                    $u->status = User::STATUS_ACTIVE;
                    $u->update();
                    Yii::$app->session->setFlash('success', Yii::t('frontend', 'Your {serviceProvider} account has been connected to your Meeting Planner account. In the future you can log in with a single click of its logo.',
    array('serviceProvider'=>$serviceTitle)));
                }
            }
        }
}
