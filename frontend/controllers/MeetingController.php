<?php

namespace frontend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingSearch;
use frontend\models\Participant;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
use frontend\models\MeetingPlaceChoice;
use frontend\models\MeetingTimeChoice;
use frontend\models\MeetingSetting;
use frontend\models\MeetingLog;
use frontend\models\UserContact;
use frontend\models\UserSetting;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MeetingController implements the CRUD actions for Meeting model.
 */
class MeetingController extends Controller
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
                        'class' => \common\filters\MeetingControl::className(), // \yii\filters\AccessControl::className(),
                        'only' => ['index','view','create','update','delete', 'decline','cancel','command','download','wizard','trash','late'],
                        'rules' => [
                          // allow authenticated users
                           [
                               'allow' => true,
                               'actions'=>['create','index','view','update','delete', 'decline','cancel','command','download','wizard','trash','late'],
                               'roles' => ['@'],
                           ],
                          [
                              'allow' => true,
                              'actions'=>['command'],
                              'roles' => ['?'],
                          ],
                          // everything else is denied
                        ],
                    ],
        ];
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionIndex()
    {
      if (Meeting::countUserMeetings(Yii::$app->user->getId())==0) {
        $this->redirect(['create']);
      }
      $planningProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT]]),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);
      $upcomingProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_CONFIRMED]]),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);
        $pastProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>Meeting::STATUS_COMPLETED]),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);
        $canceledProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>Meeting::STATUS_CANCELED]),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
        ]);
        Meeting::displayProfileHints();
        return $this->render('index', [
            'planningProvider' => $planningProvider,
            'upcomingProvider' => $upcomingProvider,
            'pastProvider' => $pastProvider,
            'canceledProvider' => $canceledProvider,
        ]);
    }

    public function actionWizard() {
      return $this->render('wizard', [

      ]);
    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      $model = $this->findModel($id);
      $model->prepareView();
      // notes always used on view panel
      $noteProvider = new ActiveDataProvider([
          'query' => MeetingNote::find()->where(['meeting_id'=>$id]),
          'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
      ]);
      $participantProvider = new ActiveDataProvider([
          'query' => Participant::find()->where(['meeting_id'=>$id]),
      ]);
      //$x = Participant::find()->where(['meeting_id'=>$id])->one();
      //var_dump($x->participant->email);exit;
      // fetch user timezone
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      if ($model->status <= Meeting::STATUS_SENT) {
        $whereStatus = MeetingPlace::getWhereStatus($model,Yii::$app->user->getId());
        //$whenStatus = MeetingTime::getWhenStatus($model,Yii::$app->user->getId());
        $timeProvider = new ActiveDataProvider([
            'query' => MeetingTime::find()->where(['meeting_id'=>$id]),
        ]);
        $placeProvider = new ActiveDataProvider([
            'query' => MeetingPlace::find()->where(['meeting_id'=>$id]),
        ]);
          return $this->render('view', [
              'model' => $model,
              'participantProvider' => $participantProvider,
              'timeProvider' => $timeProvider,
              'noteProvider' => $noteProvider,
              'placeProvider' => $placeProvider,
              'whereStatus' => $whereStatus,
              //'whenStatus' => $whenStatus,
              'viewer' => Yii::$app->user->getId(),
              'isOwner' => $model->isOwner(Yii::$app->user->getId()),
              'timezone' => $timezone,
          ]);
      } else {
        // meeting is finalized or past
        $isOwner = $model->isOwner(Yii::$app->user->getId());
        if (($model->meeting_type == Meeting::TYPE_PHONE || $model->meeting_type == Meeting::TYPE_VIDEO || $model->meeting_type == Meeting::TYPE_VIRTUAL)) {
          $noPlace = true;
          if ($isOwner) {
            // display participants contact info
            $participant = Participant::find()->where(['meeting_id'=>$id])->one();
            $contacts = UserContact::get($participant->participant_id);
          } else {
            // display organizers contact info
            $contacts = UserContact::get($model->owner_id);
          }
        } else {
          $noPlace=false;
          $contacts=[];
        }
        $chosenPlace = Meeting::getChosenPlace($id);
        if ($chosenPlace!==false) {
          $place = $chosenPlace->place;
          $gps = $chosenPlace->place->getLocation($chosenPlace->place->id);
        } else {
          $place = false;
          $gps = false;
        }
        $chosenTime = Meeting::getChosenTime($id);
        return $this->render('view_confirmed', [
            'model' => $model,
            'participantProvider' => $participantProvider,
            'noteProvider' => $noteProvider,
            'viewer' => Yii::$app->user->getId(),
            'isOwner' => $isOwner,
            'place' => $place,
            'time'=>$model->friendlyDateFromTimestamp($chosenTime->start,$timezone),
            'gps'=>$gps,
            'noPlace'=>$noPlace,
            'contacts' => $contacts,
            'contactTypes'=>UserContact::getUserContactTypeOptions(),
        ]);
      }
    }

    public function actionViewplace($id,$place_id)
    {
      $meeting_place= MeetingPlace::find()->where(['place_id'=>$place_id,'meeting_id'=>$id])->one();
      $model = $this->findModel($id);
      $model->prepareView();
        return $this->render('viewplace', [
            'model' => $model,
            'viewer' => Yii::$app->user->getId(),
            'isOwner' => $model->isOwner(Yii::$app->user->getId()),
            'place' => $meeting_place->place,
            'gps'=>$meeting_place->place->getLocation($place_id),
        ]);
    }

    public function actionTest() {
    /*  $model = new Meeting();
      return $this->render('test', [
          'model' => $model,
       ] );*/
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        // prevent creation of numerous empty meetings
        $meeting_id = Meeting::findEmptyMeeting(Yii::$app->user->getId());
        //echo $meeting_id;exit;
        if ($meeting_id===false) {
        // otherwise, create a new meeting
          $model = new Meeting();
          $model->owner_id= Yii::$app->user->getId();
          $model->sequence_id = 0;
          $model->meeting_type = 0;
          $model->save();
          $model->initializeMeetingSetting($model->id,$model->owner_id);
          $meeting_id = $model->id;
        }
        $this->redirect(['view', 'id' => $meeting_id]);
    }

    /**
     * Updates an existing Meeting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->title = $model->getMeetingTitle($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            MeetingLog::add($id,MeetingLog::ACTION_EDIT_MEETING,Yii::$app->user->getId(),0);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Meeting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionTrash($id)
    {
        $user_id = Yii::$app->user->getId();
        if ($this->findModel($id)->trash($user_id)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your meeting has been deleted.'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, we had a problem deleting your meeting.'));
          }
        return $this->redirect(['index']);
    }

    public function actionLate($id,$result=false)
    {
        if ($result) {
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We have notified the other participant(s) that you are running a few minutes late.'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, it appears we already notified the other participants that you are running late.'));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDownload($id) {
      echo Meeting::buildCalendar($id);
    }

    public function actionDecline($id) {
      $user_id = Yii::$app->user->getId();
      if ($this->findModel($id)->decline($user_id)) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your participation in this meeting has been declined and the organizer will be notified.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, we had a problem recording your decline.'));
      }
      return $this->redirect(['index']);
    }

    public function actionCancelask($id) {
      $user_id = Yii::$app->user->getId();
      $model = $this->findModel($id);
      return $this->render('cancelask', [
          'meeting_id' => $id,
          'model' => $model,
          'viewer_id' => $user_id,
      ]);
    }

    public function actionCancel($id) {
      $user_id = Yii::$app->user->getId();
      if ($this->findModel($id)->cancel($user_id)) {
          Yii::$app->getSession()->setFlash('success', Yii::t('frontend','This meeting has been canceled and everyone will be notified shortly.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, we had trouble canceling this meeting.'));
      }
      return $this->redirect(['index']);
    }

    public function actionCansend($id,$viewer_id) {
      // ajax checks if viewer can send this meeting
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->findModel($id)->canSend($viewer_id);
    }

    public function actionCanfinalize($id,$viewer_id) {
      // ajax checks if viewer can send this meeting
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      return $this->findModel($id)->canFinalize($viewer_id);
    }

    public function actionSend($id) {
      $meeting = $this->findModel($id);
      if ($meeting->canSend(Yii::$app->user->getId())) {
        $meeting->send(Yii::$app->user->getId());
        Yii::$app->getSession()->setFlash('success', 'Your meeting invitation has been sent.');
        return $this->redirect(['index']);
      } else {
        // failed
        Yii::$app->getSession()->setFlash('error', 'Sorry, your meeting invitation is not ready to send.');
        return $this->redirect(['view', 'id' => $id]);
      }
    }

    public function actionFinalize($id) {
      $meeting = $this->findModel($id);
      if ($meeting->canFinalize(Yii::$app->user->getId())) {
        $meeting->finalize(Yii::$app->user->getId());
        Yii::$app->getSession()->setFlash('success', 'Your meeting has been finalized.');
        return $this->redirect(['index']);
      } else {
        // failed
        Yii::$app->getSession()->setFlash('error', 'Sorry, your meeting invitation cannot be finalized yet.');
        return $this->redirect(['view', 'id' => $id]);
      }
    }

    public function actionVirtual($id,$state) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      // set virtual
      $meeting = $this->findModel($id);
      if ($state == 1) {
        $meeting->meeting_type = Meeting::TYPE_VIRTUAL;
        MeetingLog::add($id,MeetingLog::ACTION_MAKE_VIRTUAL,Yii::$app->user->getId(),0);
      } else {
        $meeting->meeting_type = Meeting::TYPE_OTHER;
        MeetingLog::add($id,MeetingLog::ACTION_MAKE_INPERSON,Yii::$app->user->getId(),0);
      }
      $meeting->update();
    }

    public function actionCommand($id,$cmd=0,$obj_id=0,$actor_id=0,$k=0) {
      $performAuth = true;
      $authResult = false;
      // Manage the incoming session
      if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->getId()!=$actor_id) {
          // to do: give user a choice of not logging out
          Yii::$app->user->logout();
          // to do - check that this logs out in single call
        } else {
          // user actor_id is already logged in
          $authResult = true;
          $performAuth = false;
        }
      }
      if ($performAuth) {
          $person = new \common\models\User;
          $identity = $person->findIdentity($actor_id);
          if ($identity->validateAuthKey($k)) {
            Yii::$app->user->login($identity);
            // echo 'authenticated';
            $authResult=true;
          } else {
            // echo 'fail';
            $authResult=false;
          }
      }
      if (!$authResult) {
        $this->redirect(['site/authfailure']);
      } else {
        // TO DO check if user is PASSIVE
        // if active, set SESSION to indicate log in through command
        // if PASSIVE login
        // - if no password, setflash to link to create password
        // - meeting page - flash to security limitation of that meeting view
        // - meeting index - redirect to view only that meeting (do this on other index pages too)
        $meeting = $this->findModel($id);
        switch ($cmd) {
          case Meeting::COMMAND_HOME:
            $this->goHome();
          break;
          case Meeting::COMMAND_VIEW:
            $this->redirect(['meeting/view','id'=>$id]);
          break;
          case Meeting::COMMAND_VIEW_MAP:
            // obj_id is Place model id
            $this->redirect(['meeting/viewplace','id'=>$id,'place_id'=>$obj_id]);
          break;
          case Meeting::COMMAND_FINALIZE:
            $this->redirect(['meeting/finalize','id'=>$id]);
          break;
          case Meeting::COMMAND_CANCEL:
            $this->redirect(['meeting/cancelask','id'=>$id]);
          break;
          case Meeting::COMMAND_DECLINE:
            $this->redirect(['meeting/decline','id'=>$id]);
          break;
          case Meeting::COMMAND_ACCEPT_ALL:
            MeetingTimeChoice::setAll($id,$actor_id);
            MeetingPlaceChoice::setAll($id,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
          break;
          case Meeting::COMMAND_ACCEPT_ALL_PLACES:
            MeetingPlaceChoice::setAll($id,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_ACCEPT_ALL_TIMES:
            MeetingTimeChoice::setAll($id,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_ADD_PLACE:
            $this->redirect(['meeting-place/create','meeting_id'=>$id]);
          break;
          case Meeting::COMMAND_ADD_TIME:
            $this->redirect(['meeting-time/create','meeting_id'=>$id]);
          break;
          case Meeting::COMMAND_ADD_NOTE:
            $this->redirect(['meeting-note/create','meeting_id'=>$id]);
          break;
          case Meeting::COMMAND_ADD_CONTACT:
            $this->redirect(['user-contact/index']);
          break;

          case Meeting::COMMAND_ACCEPT_PLACE:
            $mpc = MeetingPlaceChoice::find()->where(['meeting_place_id'=>$obj_id,'user_id'=>$actor_id])->one();
            MeetingPlaceChoice::set($mpc->id,MeetingPlaceChoice::STATUS_YES,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_REJECT_PLACE:
            $mpc = MeetingPlaceChoice::find()->where(['meeting_place_id'=>$obj_id,'user_id'=>$actor_id])->one();
            MeetingPlaceChoice::set($mpc->id,MeetingPlaceChoice::STATUS_NO,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_CHOOSE_PLACE:
            MeetingPlace::setChoice($id,$obj_id,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
          break;
          case Meeting::COMMAND_ACCEPT_TIME:
            $mtc = MeetingTimeChoice::find()->where(['meeting_time_id'=>$obj_id,'user_id'=>$actor_id])->one();
            MeetingTimeChoice::set($mtc->id,MeetingTimeChoice::STATUS_YES,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_REJECT_TIME:
            $mtc = MeetingTimeChoice::find()->where(['meeting_time_id'=>$obj_id,'user_id'=>$actor_id])->one();
            MeetingTimeChoice::set($mtc->id,MeetingTimeChoice::STATUS_NO,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
            break;
          case Meeting::COMMAND_CHOOSE_TIME:
            MeetingTime::setChoice($id,$obj_id,$actor_id);
            $this->redirect(['meeting/view','id'=>$id]);
          break;
          case Meeting::COMMAND_RUNNING_LATE:
            $result = Meeting::sendLateNotice($id,$actor_id);
            $this->redirect(['meeting/late','id'=>$id,'result'=>$result]);
          break;
          case Meeting::COMMAND_FOOTER_EMAIL:
            // change email settings
            // find the correct usersetting record by actor_id
            $id= UserSetting::initialize($actor_id);
            $this->redirect(['user-setting/update','id'=>$id]);
          break;
          case Meeting::COMMAND_FOOTER_BLOCK:
            // block this $obj_id (is sender_id)
            \frontend\models\UserBlock::add($actor_id,$obj_id);
            Yii::$app->getSession()->setFlash('success', 'We have blocked this user from contacting you again.');
            $this->redirect(['user-block/index']);
          break;
          case Meeting::COMMAND_FOOTER_BLOCK_ALL:
            // change setting to block all email
            UserSetting::initialize($actor_id);
            $us = UserSetting::find()->where(['user_id'=>$actor_id])->one();
            $us->no_email = UserSetting::EMAIL_NONE;
            $us->update();
            Yii::$app->getSession()->setFlash('success', 'You will no longer receive email from us. You can reverse this below.');
            $this->redirect(['user-setting/update','id'=>$us->id]);
          break;
          case Meeting::COMMAND_GO_REMINDERS:
            $this->redirect(['reminder/index']);
          break;
          default:
            $this->redirect(['site\error','meeting_id'=>$id]);
            break;
        }
      }
    }


    /**
     * Finds the Meeting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meeting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meeting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
