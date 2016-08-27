<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Request;
use frontend\models\RequestResponse;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingLog;

/**
 * RequestResponseController implements the CRUD actions for RequestResponse model.
 */
class RequestResponseController extends Controller
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
                        'actions'=>['create'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Creates a new RequestResponse model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
      $request = Request::findOne($id);
      if (!Meeting::isAttendee($request->meeting_id,Yii::$app->user->getId())) {
        $this->redirect(['site/authfailure']);
      }
        // has this user already responded
        $check = RequestResponse::find()
          ->where(['request_id'=>$id])
          ->andWhere(['responder_id'=>Yii::$app->user->getId()])
          ->count();
        if ($check>0) {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you already responded to this request.'));
          return $this->redirect(['meeting/view', 'id' => $request->meeting_id]);
        }
        if ($request->requestor_id == Yii::$app->user->getId()) {
          Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, can not respond to your own request.'));
          return $this->redirect(['meeting/view', 'id' => $request->meeting_id]);
        }
        $m = Meeting::findOne($request->meeting_id);
        $isOrganizer = $m->isOrganizer();
        if ($m->owner_id == Yii::$app->user->getId()) {
          $isOwner = true;
        } else {
          $isOwner = false;
        }
        $subject = Request::buildSubject($id);
        $model = new RequestResponse();
        $model->request_id = $id;
        $model->responder_id = Yii::$app->user->getId();
        if ($model->load(Yii::$app->request->post()) ) {
          $posted = Yii::$app->request->post();
          if (isset($posted['accept'])) {
            // accept
            $model->response = RequestResponse::RESPONSE_ACCEPT;
            $model->save();
            $request->accept($model);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Request accepted. We will update the meeting details and inform other participants.'));
          } else if (isset($posted['reject'])) {
            // reject
            $model->response = RequestResponse::RESPONSE_REJECT;
            $model->save();
            $request->reject($model);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your decline has been recorded. We will let other participants know.'));
          } else if (isset($posted['like'])) {
            // accept
            $model->response = RequestResponse::RESPONSE_LIKE;
            $model->save();
            $request->opinion(RequestResponse::RESPONSE_LIKE);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your response has been recorded. We will share with the organizers.'));
          } else if (isset($posted['neutral'])) {
            // reject
            $model->response = RequestResponse::RESPONSE_NEUTRAL;
            $model->save();
            $request->opinion(RequestResponse::RESPONSE_NEUTRAL);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your response has been recorded. We will share with the organizers.'));
          } else if (isset($posted['neutral'])) {
            // accept
            $model->response = RequestResponse::RESPONSE_DISLIKE;
            $model->save();
            $request->opinion(RequestResponse::RESPONSE_DISLIKE);
            Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your response has been recorded. We will share with the organizers.'));
          }
          return $this->redirect(['/meeting/view', 'id' => $request->meeting_id]);
        } else {
          $responseProvider = new ActiveDataProvider([
                'query' => RequestResponse::find()->where(['request_id'=>$id])->andWhere('responder_id<>'.Yii::$app->user->getId()),
                'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
                'pagination' => [
                    'pageSize' => 7,
                  ],
            ]);
            return $this->render('create', [
                'model' => $model,
                'responseProvider' => $responseProvider,
                'isOwner'=>$isOwner,
                'isOrganizer'=>$isOrganizer,
                'subject' => $subject,
                'meeting_id' => $request->meeting_id,
            ]);
        }
    }
}
