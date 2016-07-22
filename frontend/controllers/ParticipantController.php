<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Meeting;
use frontend\models\Participant;
use frontend\models\ParticipantSearch;
use frontend\models\Friend;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\base\Security;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

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
                       'actions'=>['create','delete'],
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
     * Creates a new Participant model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($meeting_id)
    {
      /*$yg = new \common\models\Yiigun();
      $result = $yg->validate('rob@gmai.com');
      var_dump($result);
      exit; */
      if (!Participant::withinLimit($meeting_id)) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of participants per meeting. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
      }
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
            if (!User::find()->where( [ 'email' => $model->email ] )->exists()) {
              // if email not already registered
              //  create new user with temporary username & password
              $temp_email_arr[] = $model->email;
              $model->username = Inflector::slug(implode('-', $temp_email_arr));
              $model->password = Yii::$app->security->generateRandomString(12);
              $model->participant_id = $model->addUser();
            } else {
              // add participant from user record
              $usr = User::find()->where( [ 'email' => $model->email ] )->one();
              $model->participant_id = $usr->id;
            }
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
