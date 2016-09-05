<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Security;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\authclient\ClientInterface;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\Participant;
use frontend\models\ParticipantSearch;
use frontend\models\Friend;
use frontend\models\Auth;
use frontend\models\UserProfile;

/**
 * ParticipantController implements the CRUD actions for Participant model.
 */
class ParticipantController extends Controller
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
                       'actions'=>['create','delete','toggleorganizer','toggleparticipant','join'],
                       'roles' => ['@'],
                   ],
                  [
                      'allow' => true,
                      'actions'=>['join','auth'],
                      'roles' => ['?'],
                  ],
                  // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Creates a new Participant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($meeting_id)
    {
      if (!Participant::withinLimit($meeting_id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of participants per meeting. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
      }
      /*$yg = new \common\models\Yiigun();
      $result = $yg->validate('rob@gmai.com');
      var_dump($result);
      exit; */
        $mtg = new Meeting();
        $title = $mtg->getMeetingTitle($meeting_id);
          $model = new Participant();
          $model->meeting_id= $meeting_id;
          $model->invited_by= Yii::$app->user->getId();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        // load friends for auto complete field
        $friends = Friend::getFriendList(Yii::$app->user->getId());
        if ($model->load(Yii::$app->request->post())) {
          $postedVars = Yii::$app->request->post();
          if (!empty($postedVars['Participant']['new_email']) && !empty($model->email)) {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Please only select either an existing participant or a friend but not both.'));
            return $this->render('create', [
                'model' => $model,
              'title' => $title,
              'friends'=>$friends,
            ]);
          } else {
            if (!empty($postedVars['Participant']['new_email'])) {
                $model->email = $postedVars['Participant']['new_email'];
            }
            $model->participant_id = User::addUserFromEmail($model->email);
            // validate the form against model rules
            if ($model->validate()) {
                // all inputs are valid
                $model->save();
                Meeting::displayNotificationHint($meeting_id);
                return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
            } else {
                // validation failed
                return $this->render('create', [
                    'model' => $model,
                  'title' => $title,
                  'friends'=>$friends,
                ]);
            }
          }
        } else {
          return $this->render('create', [
              'model' => $model,
            'title' => $title,
            'friends'=>$friends,
          ]);
        }
    }

    public function actionJoin($meeting_id,$identifier) {
      if (!Yii::$app->user->isGuest) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry you should not have access to the page you requested. Please contact support.'));
        return $this->goHome();
      }
      // to do - check mtg identifier is present
      $m = Meeting::find()
        ->where(['identifier'=>$identifier])
        ->andWhere(['id'=>$meeting_id])
        ->one();
      if (is_null($m)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry we could not find the meeting that you requested to join. Please contact support.'));
        return $this->goHome();
      }
      // set return Url
      Yii::$app->user->setReturnUrl($m->getSharingUrl());
      if (!Participant::withinLimit($meeting_id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, this meeting has reached the maximum number of participants per meeting. Please let the organizer know or contact support.'));
        return $this->goHome();
      }
      // ask them to sign up or login and then return here
      // check maximum number of invites
      // place a form for name & email and set them up as a passive participant
      $model = new Participant;
      $model->meeting_id = $meeting_id;
      if ($model->load(Yii::$app->request->post())) {
        // asking does the person joining already exist in User table
        // might have been added to invitation by organizer or might already be a registered user
        $person = User::find()->where(['email'=>$model->email])->one();
        if (!is_null($person)) {
            // are they an attendee
          if (Meeting::isAttendee($model->meeting_id,$person->id)) {
              $identity = $person->findIdentity($person->id);
              Yii::$app->user->login($identity);
              // to do - update user profile with first and last name
              $this->redirect(['meeting/view','id'=>$model->meeting_id]);
            } else {
              Yii::$app->getSession()->setFlash('warning', Yii::t('frontend','Since you have an account already, please login below.'));
              return $this->redirect(['/site/login']);
            }
          }
        $postedVars = Yii::$app->request->post();
        // store first and last fields
        // if no first or last name - flash
        $validationError = false;
        if (!empty($postedVars['Participant']['firstname'])) {
            $model->firstname = $postedVars['Participant']['firstname'];
        } else {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Please provide your first name below.'));
          $validationError=true;
        }
        if (!empty($postedVars['Participant']['lastname'])) {
            $model->lastname = $postedVars['Participant']['lastname'];
        } else {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Please provide your last name below.'));
          $validationError=true;
        }
        $model->invited_by = $m->owner_id;
        $model->status = Participant::STATUS_DEFAULT;
        if (!$validationError && $model->validate()) {
          $model->participant_id = User::addUserFromEmail($model->email);          
          $model->save();
          // look up email to see if they exist
          Meeting::displayNotificationHint($meeting_id);
          $user = User::findOne($model->participant_id);
          Yii::$app->user->login($user);
          return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
        }
      }
      return $this->render('join', [
          'model' => $model,
      ]);
    }

    /**
     * Deletes an existing Participant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionToggleorganizer($id,$val) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      // change setting
      $p=Participant::findOne($id);
      if ($p->meeting->isOrganizer()) {
        $p->email = $p->participant->email;
        if ($val==1) {
          $p->participant_type=Participant::TYPE_ORGANIZER;
        } else {
          $p->participant_type=Participant::TYPE_DEFAULT;
        }
        $p->update();
        return true;
      } else {
        return false;
      }

    }

    public function actionToggleparticipant($id,$val) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      // change setting
      $p=Participant::findOne($id);
      if ($p->meeting->isOrganizer()) {
        $p->email = $p->participant->email;
        if ($val==0) {
          if ($p->status == Participant::STATUS_DECLINED) {
              $p->status=Participant::STATUS_DECLINED_REMOVED;
          } else {
            $p->status=Participant::STATUS_REMOVED;
          }
        } else {
          if ($p->status == Participant::STATUS_DECLINED_REMOVED) {
              $p->status=Participant::STATUS_DECLINED;
          } else {
            $p->status=Participant::STATUS_DEFAULT;
          }
        }
        $p->update();
        return true;
      } else {
        return false;
      }
    }

    /**
     * Finds the Participant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Participant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Participant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
