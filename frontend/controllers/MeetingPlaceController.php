<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Meeting;
use frontend\models\MeetingPlace;
use frontend\models\MeetingPlaceSearch;
use frontend\models\MeetingLog;
use frontend\models\Place;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
/**
 * MeetingPlaceController implements the CRUD actions for MeetingPlace model.
 */
class MeetingPlaceController extends Controller
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
                        'actions'=>['create','delete','choose','choosetemp','insertplace','add','addgp','loadchoices'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Creates a new MeetingPlace model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
     public function actionCreate($meeting_id)
     {
       if (!MeetingPlace::withinLimit($meeting_id)) {
         Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of places per meeting. Contact support if you need additional help or want to offer feedback.'));
         return $this->redirect(['/meeting/view', 'id' => $meeting_id]);
       }
       $mtg = new Meeting();
       $title = $mtg->getMeetingTitle($meeting_id);
         $model = new MeetingPlace();
         $model->meeting_id= $meeting_id;
         $model->suggested_by= Yii::$app->user->getId();
         $model->status = MeetingPlace::STATUS_SUGGESTED;
         $posted_form = Yii::$app->request->post();
         if ($model->load($posted_form)) {
          // check if both are chosen and return an error
           if ($model->place_id<>'' and $posted_form['MeetingPlace']['google_place_id']<>'') {
             $model->addErrors(['place_id'=>Yii::t('frontend','Please choose one or the other')]);
             return $this->render('create', [
                  'model' => $model,
                   'title' => $title,
              ]);
           }
           if ($posted_form['MeetingPlace']['google_place_id']<>'') {
             // a google place is selected
             // is google place already in the Place database?
             // or, can we create a new place for this Google Place
             $model->place_id = Place::googlePlaceSuggested($posted_form['MeetingPlace']);
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
               ]);
           }
         } else {
           return $this->render('create', [
               'model' => $model,
             'title' => $title,
           ]);
         }
     }

    /**
     * Deletes an existing MeetingPlace model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionChoose($id,$val) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $parts = explode('_', $val);
      // relies on naming of mp button id
      $mp_id = (int)$parts[2]; // get # from mp_plc_#
      $meeting_id = (int)$id;
      $mtg=Meeting::find()->where(['id'=>$meeting_id])->one();
      if (Yii::$app->user->getId()!=$mtg->owner_id &&
        !$mtg->meetingSettings['participant_choose_place']) return false;
      foreach ($mtg->meetingPlaces as $mp) {
        if ($mp->id == $mp_id) {
          $mp->status = MeetingPlace::STATUS_SELECTED;
          MeetingLog::add($meeting_id,MeetingLog::ACTION_CHOOSE_PLACE,Yii::$app->user->getId(),$mp_id);
        }
        else {
          if ($mp->status == MeetingPlace::STATUS_SELECTED) {
              $mp->status = MeetingPlace::STATUS_SUGGESTED;
          }
        }
        $mp->save();
      }
      return true;
    }

    public function actionAdd($id,$place_id=0) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if ($place_id>0) {
        $model = new MeetingPlace();
        $model->meeting_id= $id;
        $model->place_id=$place_id;
        $model->suggested_by= Yii::$app->user->getId();
        $model->availability = 0;
        $model->status = MeetingPlace::STATUS_SUGGESTED;
        $model->save();
        return true;
      } else {
        return false;
      }
    }

    public function actionInsertplace($id) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      if (!Meeting::isAttendee($id,Yii::$app->user->getId())) {
        return false;
      }
      $meeting_id = $id;
      $model=Meeting::findOne($id);
      $placeProvider = new ActiveDataProvider([
          'query' => MeetingPlace::find()->where(['meeting_id'=>$id])
            ->andWhere(['status'=>[MeetingPlace::STATUS_SUGGESTED,MeetingPlace::STATUS_SELECTED]]),
          'sort' => [
            'defaultOrder' => [
              'availability'=>SORT_DESC
            ]
          ],
      ]);
      $whereStatus = MeetingPlace::getWhereStatus($model,Yii::$app->user->getId());
      $result = ListView::widget([
             'dataProvider' => $placeProvider,
             'itemOptions' => ['class' => 'item'],
             'layout' => '{items}',
             'itemView' => '/meeting-place/_list',
             'viewParams' => ['placeCount'=>$placeProvider->count,
             'isOwner'=>$model->isOwner(Yii::$app->user->getId()),
             'participant_choose_place'=>$model->meetingSettings['participant_choose_place'],
             'whereStatus'=>$whereStatus],
         ]) ;
         return $result;
    }

    public function actionAddgp($id=0,$gp_id='',$name='',$website='',$vicinity='',$full_address='',$location='') {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $place_id = Place::getGooglePlaceId(urldecode($gp_id),urldecode($name),urldecode($website),urldecode($vicinity),urldecode($full_address),urldecode($location));
      if ($place_id!==false) {
        $model = new MeetingPlace();
        $model->meeting_id= $id;
        $model->place_id=$place_id;
        $model->suggested_by= Yii::$app->user->getId();
        $model->availability = 0;
        $model->status = MeetingPlace::STATUS_SUGGESTED;
        $model->save();
        return true;
      }
    return false;
    }

    public function actionLoadchoices($id) {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      $model=Meeting::findOne($id);
      $placeProvider = new ActiveDataProvider([
          'query' => MeetingPlace::find()->where(['meeting_id'=>$id])
            ->andWhere(['status'=>[MeetingPlace::STATUS_SUGGESTED,MeetingPlace::STATUS_SELECTED]]),
          'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
      ]);
      if ($placeProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_place'])) {
        return $this->renderPartial('_choices', [
              'model'=>$model,
          ]);
      } else {
        return false;
      }
    }

    /**
     * Finds the MeetingPlace model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MeetingPlace the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingPlace::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
