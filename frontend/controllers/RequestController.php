<?php

namespace frontend\controllers;

use Yii;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingLog;
use frontend\models\MeetingPlace;
use frontend\models\MeetingSetting;
use frontend\models\Request;
use frontend\models\RequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;


/**
 * RequestController implements the CRUD actions for Request model.
 */
class RequestController extends Controller
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
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'actions'=>['view','create','update','index','withdraw'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Lists all Request models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        $meeting_id = Yii::$app->request->queryParams['meeting_id'];
        if (!Meeting::isAttendee($meeting_id,Yii::$app->user->getId())) {
          $this->redirect(['site/authfailure']);
        }
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider = new ActiveDataProvider([
              'query' => Request::find()->where(['meeting_id'=>$meeting_id])
                ->andWhere(['status'=>Request::STATUS_OPEN]),
              //'sort'=> ['defaultOrder' => ['created_at'=>SORT_ASC]],
          ]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'meeting_id' => $meeting_id,
        ]);
    }

    /**
     * Displays a single Request model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model=$this->findModel($id);
        if (!Meeting::isAttendee($model->meeting_id,Yii::$app->user->getId())) {
          $this->redirect(['site/authfailure']);
        }
        $model->meeting->setViewer();
        $meetingSettings = MeetingSetting::find()->where(['meeting_id'=>$model->meeting_id])->one();
        $requestor = MiscHelpers::getDisplayName($model->requestor_id);
        $content = Request::buildSubject($id);
        return $this->render('view', [
            'model' => $model,
            'requestor'=>$requestor,
            'content'=>$content,
            'meetingSettings'=>$meetingSettings,
        ]);
    }

    /**
     * Creates a new Request model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($meeting_id)
    {
        // verify is attendee
        if (!Meeting::isAttendee($meeting_id,Yii::$app->user->getId())) {
          $this->redirect(['site/authfailure']);
        }
        if (Request::countRequestorOpen($meeting_id,Yii::$app->user->getId())>0) {
            $r = Request::find()
              ->where(['meeting_id'=>$meeting_id])
              ->andWhere(['requestor_id'=>Yii::$app->user->getId()])
              ->andWhere(['status'=>Request::STATUS_OPEN])
              ->one();
            Yii::$app->getSession()->setFlash('info', Yii::t('frontend','You already have an existing request below.'));
              return $this->redirect(['view','id'=>$r->id]);
        }
        $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
        $model = new Request();
        $model->meeting_id=$meeting_id;
        $meeting = Meeting::findOne($meeting_id);
        $chosenTime = Meeting::getChosenTime($meeting_id);
        $countPlaces = count($meeting->meetingPlaces);
        $countTimes = count($meeting->meetingTimes);
        for ($i=1;$i<12;$i++) {
          // later times
          if ($i<4 || $i%2 == 0) {
            $altTimesList[$chosenTime->start+($i*15*60)]=Meeting::friendlyDateFromTimestamp($chosenTime->start+($i*15*60),$timezone,false);
          }
          // earlier times
          $earlierIndex = ((12-$i)*-15);
          if ($i%2 == 0 || $i>=9) {
            $altTimesList[$chosenTime->start+($earlierIndex*60)]=Meeting::friendlyDateFromTimestamp($chosenTime->start+($earlierIndex*60),$timezone,false);
          }
        }
        $altTimesList[$chosenTime->start]='────────────────────';
        $altTimesList[-1000]=Yii::t('frontend','Select an alternate time below');
        ksort($altTimesList);
        $places[0]=Yii::t('frontend','No, keep the same place');
        foreach ($meeting->meetingPlaces as $p) {
          if ($p->status <> MeetingPlace::STATUS_SELECTED) {
              $places[$p->id]=$p->place->name;
          }
        }
        $times[0] = Yii::t('frontend','select a different time');
        foreach ($meeting->meetingTimes as $t) {
          $times[$t->id] = Meeting::friendlyDateFromTimestamp($t->start,$timezone);
        }
        $model->requestor_id = Yii::$app->user->getId();
        $model->status = Request::STATUS_OPEN;
        if ($model->load(Yii::$app->request->post())) {
          if ($model->time_adjustment == Request::TIME_ADJUST_ABIT && $model->alternate_time == -1000) {
            $model->time_adjustment = Request::TIME_ADJUST_NONE;
          }
          if (($model->time_adjustment == Request::TIME_ADJUST_NONE) && $model->meeting_place_id == 0) {
              Yii::$app->getSession()->setFlash('error', Yii::t('frontend','You must request a change for a valid submission.'));
            return $this->render('create', [
                'model' => $model,
                'places' => $places,
                'times' => $times,
                'altTimesList' => $altTimesList,
                'countPlaces' => $countPlaces,
                'countTimes' => $countTimes,
                'currentStart' => $chosenTime->start,
                'currentStartStr' => Meeting::friendlyDateFromTimestamp($chosenTime->start,$timezone,false),
            ]);
          } else {
            $model->save();
            MeetingLog::add($model->meeting_id,MeetingLog::ACTION_REQUEST_CREATE,Yii::$app->user->getId(),$model->id);
            $model->create();
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','We are sending your request to other participants now.'));
             return $this->redirect(['meeting/view', 'id' => $model->meeting_id]);
          }
        } else {
            return $this->render('create', [
                'model' => $model,
                'places' => $places,
                'times' => $times,
                'altTimesList' => $altTimesList,
                'countPlaces' => $countPlaces,
                'countTimes' => $countTimes,
                'currentStart' => $chosenTime->start,
                'currentStartStr' => Meeting::friendlyDateFromTimestamp($chosenTime->start,$timezone,false),
            ]);
        }
    }

    /**
     * Updates an existing Request model.
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

    public function actionWithdraw($id)
    {
        $model=$this->findModel($id);
        if (!$model->requestor_id == Yii::$app->user->getId()) {
          $this->redirect(['site/authfailure']);
        }
        $model->withdraw($id);
        Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your requested meeting change has been withdrawn.'));
        return $this->redirect(['meeting/view', 'id' => $model->meeting_id]);
    }
    /**
     * Finds the Request model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Request the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Request::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
