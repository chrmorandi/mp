<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\MiscHelpers;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\MeetingSearch;
use frontend\models\Friend;
use frontend\models\Participant;
use frontend\models\Request;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingActivity;
use frontend\models\MeetingActivityChoice;
use frontend\models\MeetingTime;
use frontend\models\MeetingPlaceChoice;
use frontend\models\MeetingTimeChoice;
use frontend\models\MeetingSetting;
use frontend\models\MeetingLog;
use frontend\models\UserContact;
use frontend\models\UserSetting;

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
                        'class' => \yii\filters\AccessControl::className(), // \common\filters\MeetingControl::className(),
                        'rules' => [
                          // allow authenticated users
                           [
                               'allow' => true,
                               'actions'=>['create','createactivity','index','view','viewplace','removeplace','viewactivity','removeactivity','update','delete', 'decline','cancel','cancelask','command','download','trash','late','cansend','canfinalize','send','finalize','virtual','reopen','reschedule','repeat','resend','identity','updatewhat','scheduleme'],
                               'roles' => ['@'],
                           ],
                          [
                              'allow' => true,
                              'actions'=>['command','identity','scheduleme'],
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
      $tab ='planning';
      if (isset(Yii::$app->request->queryParams['tab'])) {
        $tab =Yii::$app->request->queryParams['tab'];
      }
      $planningProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT]])->distinct(),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
                'params' => array_merge($_GET, ['tab' => 'planning']),
              ],
        ]);
      $upcomingProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_CONFIRMED]])->distinct(),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_ASC]],
            'pagination' => [
                'pageSize' => 7,
                'params' => array_merge($_GET, ['tab' => 'upcoming']),
              ],
        ]);
        $pastProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>[Meeting::STATUS_COMPLETED,Meeting::STATUS_EXPIRED]])->distinct(),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
                'params' => array_merge($_GET, ['tab' => 'past']),
              ],
        ]);
        $canceledProvider = new ActiveDataProvider([
            'query' => Meeting::find()->joinWith('participants')->where(['owner_id'=>Yii::$app->user->getId()])->orWhere(['participant_id'=>Yii::$app->user->getId()])->andWhere(['meeting.status'=>Meeting::STATUS_CANCELED])->distinct(),
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
                'params' => array_merge($_GET, ['tab' => 'canceled']),
              ],
        ]);
        // fetch user timezone
        $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
        Meeting::displayProfileHints();
        return $this->render('index', [
            'planningProvider' => $planningProvider,
            'upcomingProvider' => $upcomingProvider,
            'pastProvider' => $pastProvider,
            'canceledProvider' => $canceledProvider,
            'tab' => $tab,
            'timezone'=>$timezone,
        ]);
    }

    public function actionScheduleme() {
      $username = Yii::$app->request->getPathInfo();
      $u = User::find()
        ->where(['username'=>$username])
        ->one();
      if (is_null($u)) {
        return $this->goHome();
      } elseif (!Yii::$app->user->isGuest) {
          if (Yii::$app->user->getId()==$u->id) {
            Yii::$app->getSession()->setFlash('info', Yii::t('frontend','Welcome to your public scheduling page.'));
          }
      }
      $userprofile = \frontend\models\UserProfile::find()
        ->where(['user_id'=>$u->id])
        ->one();
      Yii::$app->user->setReturnUrl(['meeting/create/','with'=>$u->username]);
      return $this->render('scheduleme', [
        'user'=>$u,
        'displayName'=> MiscHelpers::getDisplayName($u->id,true),
        'userprofile' => $userprofile,
      ]);
    }

    public function actionIdentity()
    {
      // fetch path
      list($username,$identifier) = explode("/",Yii::$app->request->getPathInfo());
      // verify the meeting identifier
      $m = Meeting::find()
        ->where(['identifier'=>$identifier])
        ->one();
      $un = str_replace(' ', '', $m->owner->username);
      if (is_null($m) || ($un != $username)) {
        // access failure
        return $this->redirect(['site/authfailure']);
      }
      // identifier is authentic
      if (Yii::$app->user->isGuest) {
        // redir to Participant join form
        return $this->redirect(['/participant/join','meeting_id'=>$m->id,'identifier'=>$identifier]);
      } else {
        $user_id = Yii::$app->user->getId();
        if (!Meeting::isAttendee($m->id,$user_id)) {
            // if not an attendee -- add them as a participant
            Participant::add($m->id,$user_id,$m->owner_id);
        }
        return $this->actionView($m->id);
      }

        // if they are - redirect them to the view below
        // then redirect them to the view

    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
      if (isset(Yii::$app->request->queryParams['tab'])) {
        $tab =Yii::$app->request->queryParams['tab'];
      } else {
        $tab ='details';
      }
      $model = $this->findModel($id);
      $meetingSettings = MeetingSetting::find()->where(['meeting_id'=>$id])->one();
      if (!$model->isAttendee($id,Yii::$app->user->getId())) {
        $this->redirect(['site/authfailure']);
      }
      $model->prepareView();
      $pStatus = $model->getParticipantStatus(Yii::$app->user->getId());
      if (!$model->isOrganizer() && $pStatus!=Participant::STATUS_DEFAULT) {
        if ($pStatus == Participant::STATUS_DECLINED || $pStatus ==Participant::STATUS_DECLINED_REMOVED) {
            Yii::$app->getSession()->setFlash('warning', Yii::t('frontend','You declined participation in this meeting.'));
        } else {
          Yii::$app->getSession()->setFlash('danger', Yii::t('frontend','You were removed from this meeting.'));
        }
      }
      // notes always used on view panel
      $noteProvider = new ActiveDataProvider([
          'query' => MeetingNote::find()->where(['meeting_id'=>$id]),
          'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
      ]);
      $participantProvider = new ActiveDataProvider([
          'query' => Participant::find()->where(['meeting_id'=>$id]),
          'sort'=> ['defaultOrder' => ['participant_type'=>SORT_DESC,'status'=>SORT_ASC]],
      ]);
      //$x = Participant::find()->where(['meeting_id'=>$id])->one();
      //var_dump($x->participant->email);exit;
      // fetch user timezone
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      // prepare participant form
      $participant = new Participant();
      $participant->meeting_id= $model->id;
      $friends = Friend::getFriendList(Yii::$app->user->getId());
      // prepare meeting time form
      $meetingTime = new MeetingTime();
      $meetingTime->tz_current = $timezone;
      $meetingTime->duration = 1;
      $meetingTime->meeting_id= $model->id;
      $meetingTime->suggested_by= Yii::$app->user->getId();
      $meetingTime->status = MeetingTime::STATUS_SUGGESTED;
      $meetingTime->start = Date('M d, Y',time()+3*24*3600);
      $meetingTime->start_time = '9:00 am';
      // prepare meeting place form
      $meetingPlace = new MeetingPlace();
      $meetingPlace->meeting_id= $model->id;
      $meetingPlace->suggested_by= Yii::$app->user->getId();
      $meetingPlace->status = MeetingPlace::STATUS_SUGGESTED;
      // prepare meeting activity form
      $meetingActivity = new MeetingActivity();
      $meetingActivity->meeting_id= $model->id;
      $meetingActivity->suggested_by= Yii::$app->user->getId();
      $meetingActivity->status = MeetingActivity::STATUS_SUGGESTED;
      if ($model->status <= Meeting::STATUS_SENT) {
        if ($model->isOrganizer() && ($model->status == Meeting::STATUS_SENT) && !$model->isSomeoneAvailable()) {
          Yii::$app->getSession()->setFlash('danger', Yii::t('frontend','None of the participants are available for the meeting\'s current options.'));
        }
        $whereStatus = MeetingPlace::getWhereStatus($model,Yii::$app->user->getId());
        $whenStatus = MeetingTime::getWhenStatus($model,Yii::$app->user->getId());
        $activityStatus = MeetingActivity::getActivityStatus($model,Yii::$app->user->getId());
        $timeProvider = new ActiveDataProvider([
            'query' => MeetingTime::find()->where(['meeting_id'=>$id])
              ->andWhere(['status'=>[MeetingTime::STATUS_SUGGESTED,MeetingTime::STATUS_SELECTED]]),
            'sort' => [
              'defaultOrder' => [
                'availability'=>SORT_DESC
              ]
            ],
        ]);
        if ($model->is_activity == Meeting::IS_ACTIVITY) {
          $activityProvider = new ActiveDataProvider([
              'query' => MeetingActivity::find()->where(['meeting_id'=>$id])
                ->andWhere(['status'=>[MeetingActivity::STATUS_SUGGESTED,MeetingActivity::STATUS_SELECTED]]),
              'sort' => [
                'defaultOrder' => [
                  'availability'=>SORT_DESC
                ]
              ],
          ]);
        } else {
          $activityProvider = null;
        }
        $placeProvider = new ActiveDataProvider([
            'query' => MeetingPlace::find()->where(['meeting_id'=>$id])
              ->andWhere(['status'=>[MeetingPlace::STATUS_SUGGESTED,MeetingPlace::STATUS_SELECTED]]),
            'sort' => [
              'defaultOrder' => [
                'availability'=>SORT_DESC
              ]
            ],
        ]);
          return $this->render('view', [
              'tab'=>$tab,
              'model' => $model,
              'meetingSettings' => $meetingSettings,
              'participantProvider' => $participantProvider,
              'timeProvider' => $timeProvider,
              'activityProvider' => $activityProvider,
              'noteProvider' => $noteProvider,
              'placeProvider' => $placeProvider,
              'whereStatus' => $whereStatus,
              'whenStatus' => $whenStatus,
              'activityStatus' => $activityStatus,
              'viewer' => Yii::$app->user->getId(),
              'isOwner' => $model->isOwner(Yii::$app->user->getId()),
              'timezone' => $timezone,
              'participant'=>$participant,
              'friends'=>$friends,
              'meetingTime'=>$meetingTime,
              'meetingPlace'=>$meetingPlace,
              'meetingActivity'=>$meetingActivity,
          ]);
      } else {
        if ($model->isOrganizer() && !$model->isSomeoneAvailable()) {
          Yii::$app->getSession()->setFlash('danger', Yii::t('frontend','None of the participants are available for this meeting.'));
        }
        // meeting is finalized or past
        if (Request::countOpen($id)) {
            Yii::$app->getSession()->setFlash('warning', Yii::t('frontend','Changes have been requested for this meeting. <a href="{url}">View them</a>.',['url'=>Url::to(['/request/index/','meeting_id'=>$id])]));
        }
        $isOwner = $model->isOwner(Yii::$app->user->getId());
        if (($model->isVirtual())) {
          $contactListObj = $model->getContactListObj(Yii::$app->user->getId(),$isOwner);
        } else {
          $contactListObj = null;
        }
        $chosenPlace = Meeting::getChosenPlace($id);
        if ($chosenPlace!==false) {
          $place = $chosenPlace->place;
          $gps = $chosenPlace->place->getLocation($chosenPlace->place->id);
          $noPlace = false;
        } else {
          $place = false;
          $noPlace = true;
          $gps = false;
        }
        $chosenTime = Meeting::getChosenTime($id);
        return $this->render('view_confirmed', [
            'tab'=>$tab,
            'model' => $model,
            'meetingSettings' => $meetingSettings,
            'participantProvider' => $participantProvider,
            'noteProvider' => $noteProvider,
            'viewer' => Yii::$app->user->getId(),
            'isOwner' => $isOwner,
            'place' => $place,
            'time'=>$model->friendlyDateFromTimestamp($chosenTime->start,$timezone),
            'showRunningLate'=>($chosenTime->start - time() > 0 && $chosenTime->start -time() <10800 )?true:false,
            'isPast'=>($chosenTime->start - time() < 0)?true:false,
            'gps'=>$gps,
            'noPlace'=>$noPlace,
            'contactListObj' => $contactListObj,
            //'contactTypes'=>UserContact::getUserContactTypeOptions(),
            'timezone'=>$timezone,
            'participant'=>$participant,
            'friends'=>$friends,
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

    public function actionRemoveplace($meeting_id,$place_id)
    {
      $result= MeetingPlace::removePlace($meeting_id,$place_id);
      if ($result) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','The meeting place has been removed.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you may not have access to removing meeting places.'));
      }
      return $this->redirect(['view','id'=>$meeting_id]);
    }

    public function actionViewactivity($id,$activity_id)
    {
      $meeting_activity= MeetingActivity::find()->where(['id'=>$activity_id,'meeting_id'=>$id])->one();
      $model = $this->findModel($id);
      $model->prepareView();
        return $this->render('viewactivity', [
            'model' => $model,
            'viewer' => Yii::$app->user->getId(),
            'isOwner' => $model->isOwner(Yii::$app->user->getId()),
            'activity' => $meeting_activity,
            'title'=>$meeting_activity->activity,
        ]);
    }

    public function actionRemoveactivity($meeting_id,$activity_id)
    {
      $result= MeetingActivity::removeActivity($meeting_id,$activity_id);
      if ($result) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','The meeting activity has been removed.'));
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you may not have access to removing meeting activitys.'));
      }
      return $this->redirect(['view','id'=>$meeting_id]);
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($with = '')
    {
        if (!Meeting::withinLimit(Yii::$app->user->getId())) {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, there are limits on how quickly you can create meetings. Visit support if you need assistance.'));
          return $this->redirect(['index']);
        }
        if ($with<>'') {
          $u = User::find()
            ->where(['username'=>$with])
            ->one();
            if (!is_null($u)) {
              $with_id =$u->id;
            } else {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, we could not locate anyone by that name. Visit support if you need assistance.'));
              $with_id =0;
            }
        } else {
          $with_id =0;
        }
        // prevent creation of numerous empty meetings
        $meeting_id = Meeting::findEmptyMeeting(Yii::$app->user->getId(),$with_id);
        if ($meeting_id===false) {
        // otherwise, create a new meeting
          $model = new Meeting();
          $model->owner_id= Yii::$app->user->getId();
          $model->sequence_id = 0;
          $model->meeting_type = 0;
          $model->is_activity = Meeting::NOT_ACTIVITY;
          $model->subject = Meeting::DEFAULT_SUBJECT;
          $model->save();
          $model->initializeMeetingSetting($model->id,$model->owner_id);
          $meeting_id = $model->id;
        }
        if ($with_id!=0) {
            Participant::add($meeting_id,$with_id,Yii::$app->user->getId());
        }
        $this->redirect(['view', 'id' => $meeting_id]);
    }

    public function actionCreateactivity()
    {
        if (!Meeting::withinLimit(Yii::$app->user->getId())) {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, there are limits on how quickly you can create meetings. Visit support if you need assistance.'));
          return $this->redirect(['index']);
        }
        // prevent creation of numerous empty meetings
        $meeting_id = Meeting::findEmptyActivity(Yii::$app->user->getId());
        if ($meeting_id===false) {
        // otherwise, create a new meeting
          $model = new Meeting();
          $model->owner_id= Yii::$app->user->getId();
          $model->sequence_id = 0;
          $model->is_activity = Meeting::IS_ACTIVITY;
          $model->subject = Meeting::DEFAULT_SUBJECT;
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

    public function actionDownload($id,$actor_id=false) {
      // sometimes user arrives from _grid w/o actor_id or other times from email
      if ($actor_id===false) {
        $actor_id = Yii::$app->user->getId();
      }
      if ($actor_id == Yii::$app->user->getId() && Meeting::isAttendee($id,$actor_id)) {
          Meeting::prepareDownloadIcs($id,$actor_id);
      }
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
      // check that owner is verified
      $u = User::findOne($meeting->owner_id);
      if ($u->status==User::STATUS_UNVERIFIED) {
        User::sendVerifyEmail($u->id,$meeting->id);
        Yii::$app->getSession()->setFlash('error', 'Sorry, before you can send, we need you to verify your email address by clicking the button in the message we just sent you.');
        return $this->redirect(['view', 'id' => $id]);
      }
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
      // check if owner is finalizing and they are verified
      if ($meeting->owner_id == Yii::$app->user->getId()) {
        $u = User::findOne($meeting->owner_id);
        if ($u->status==User::STATUS_UNVERIFIED) {
          User::sendVerifyEmail($u->id,$meeting->id);
          Yii::$app->getSession()->setFlash('error', 'Sorry, before you can send, we need you to verify your email address by clicking the button in the message we just sent you.');
          return $this->redirect(['view', 'id' => $id]);
        }
      }
      if ($meeting->canFinalize(Yii::$app->user->getId())) {
        $meeting->finalize(Yii::$app->user->getId());
        Yii::$app->getSession()->setFlash('success', 'Your meeting has been finalized.');
        return $this->redirect(['/meeting/?tab=upcoming']);
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
      return true;
    }

    public function actionReschedule($id) {
        // check that person has permissions
        $m = $this->findModel($id);
        $m->setViewer();
        // to do - allow participants to reopen if meeting settings allow it
        // also check reschedule()
        if ($m->viewer == Meeting::VIEWER_ORGANIZER) {
          $new_meeting_id = $m->reschedule();
          if ($new_meeting_id!==false) {
              Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Plan times for your rescheduled meeting below.'));
              return $this->redirect(['view', 'id' => $new_meeting_id]);
          } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, there was a problem rescheduling the meeting.'));
          }
        } else {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you are not allowed to do this.'));
          return $this->redirect(['view', 'id' => $id]);
        }
    }

    public function actionRepeat($id) {
        // check that person has permissions
        $m = $this->findModel($id);
        $m->setViewer();
        $user_id = Yii::$app->user->getId();
        if (!MeetingLog::withinActionLimit($id,MeetingLog::ACTION_REPEAT,$user_id,7)
          || !MeetingLog::withinActionTimeLimit($id,MeetingLog::ACTION_REPEAT,$user_id,3) ) {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you are not allowed to repeat a meeting so frequently or this many times. Please contact us if you need this extended.'));
            return $this->redirect(['view', 'id' => $id]);
        }
        if ($m->isAttendee($id,$user_id)) {
          $new_meeting_id = $m->repeat();
          if ($new_meeting_id!==false) {
              Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We suggested two identical upcoming meeting times. You can also add more below.'));
              return $this->redirect(['view', 'id' => $new_meeting_id]);
          } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, there was a problem repeating this meeting.'));
          }
        } else {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you are not allowed to do this.'));
          return $this->redirect(['view', 'id' => $id]);
        }
    }

    public function actionReopen($id) {
      $m = $this->findModel($id);
      $m->setViewer();
      // also check reopen()
      if ($m->viewer == Meeting::VIEWER_ORGANIZER || $m->meetingSettings->participant_reopen) {
        if ($m->reopen()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','The meeting has now been reopened so you can make changes.'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you are not allowed to reopen a meeting this many times. Try creating a new meeting.'));
        }
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you are not allowed to do this.'));
      }
      return $this->redirect(['view', 'id' => $id]);
    }

    public function actionResend($id) {
      if (Meeting::Resend($id)) {
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Details about the meeting have been resent.'));
        return $this->redirect(['view', 'id' => $id]);
      } else {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you can only resend the invitation so many times. Please contact support.'));
        return $this->redirect(['index']);
      }
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
        if ($id!=0) {
          $meeting = $this->findModel($id);
        }
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
          case Meeting::COMMAND_VIEW_REQUEST:
            // obj_id is Request model id
            $this->redirect(['request-response/create','id'=>$obj_id]);
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
            $this->redirect(['/user-contact/create']);
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
            if ($obj_id>0) {
              \frontend\models\UserBlock::add($actor_id,$obj_id);
              Yii::$app->getSession()->setFlash('success', 'We have blocked this user from contacting you again.');
            } else {
              Yii::$app->getSession()->setFlash('error', 'Sorry, there was a problem. Please visit our support page and tell us what you were doing.');
            }
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
          case Meeting::COMMAND_NO_UPDATES:
            // change setting to block all email
            UserSetting::initialize($actor_id);
            $us = UserSetting::find()->where(['user_id'=>$actor_id])->one();
            $us->no_updates = UserSetting::EMAIL_NONE;
            $us->update();
            \backend\models\Message::respond($obj_id,$actor_id,\backend\models\Message::RESPONSE_NO_UPDATES);
            Yii::$app->getSession()->setFlash('success', 'You will no longer receive product updates from us. You can reverse this below.');
            $this->redirect(['user-setting/update','id'=>$us->id]);
            break;
          case Meeting::COMMAND_NO_NEWSLETTER:
            // change setting to block all email
            UserSetting::initialize($actor_id);
            $us = UserSetting::find()->where(['user_id'=>$actor_id])->one();
            $us->no_newsletter = UserSetting::EMAIL_NONE;
            $us->update();
            Yii::$app->getSession()->setFlash('success', 'You will no longer receive product updates from us. You can reverse this below.');
            $this->redirect(['user-setting/update','id'=>$us->id]);
            break;
          case Meeting::COMMAND_VERIFY_EMAIL:
            $u  = \common\models\User::findOne($actor_id);
            $u->status = \common\models\User::STATUS_ACTIVE;
            $u->update();
            $this->redirect(['meeting/view','id'=>$id]);
          break;
          case Meeting::COMMAND_GO_REMINDERS:
            $this->redirect(['reminder/index']);
          break;
          case Meeting::COMMAND_RESPOND_MESSAGE:
            $this->redirect(\backend\models\Message::respond($obj_id,$actor_id,\backend\models\Message::RESPONSE_YES));
            break;
          case Meeting::COMMAND_DOWNLOAD_ICS:
            $this->redirect(['meeting/download','id'=>$id,'actor_id'=>$actor_id]);
            break;
          default:
            $this->redirect(['site\error','meeting_id'=>$id]);
            break;
        }
      }
    }

    public function actionUpdatewhat($id,$subject='',$message='') {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if (!Meeting::isAttendee($id,Yii::$app->user->getId())) {
        return false;
      }
      $m=Meeting::findOne($id);
      $m->subject = $subject;
      $m->message = $message;
      $m->update();
      return true;
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
