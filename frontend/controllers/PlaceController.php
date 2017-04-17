<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Place;
use frontend\models\PlaceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\User;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;


/**
 * PlaceController implements the CRUD actions for Place model.
 */
class PlaceController extends Controller
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
                        'actions' => ['index','yours','create', 'create_geo','create_place_google','update','view','slug','delete'],
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    /**
     * Lists all Place models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['yours']);
    }

    public function actionYours()
    {
      $query = Place::find()->joinWith('userPlaces')->where(['user_id' => Yii::$app->user->getId()]);
      $searchModel = new PlaceSearch();
         $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => ['pageSize' => 7],
         ]);

         return $this->render('yours',[
            'dataProvider' => $dataProvider,
            'searchModel'=>$searchModel,
         ]);
    }

    public function actionView($id)
    {
        $model=$this->findModel($id);
        $gps = $model->getLocation($model->id);
        return $this->render('view', [
            'model' => $model,
            'gps'=> $gps,
        ]);
    }

    public function actionSlug($slug)
    {
      $model = Place::find()->where(['slug'=>$slug])->one();
      if (!is_null($model)) {
        $gps = $model->getLocation($model->id);
          return $this->render('view', [
              'model' => $model,
              'gps'=> $gps,
          ]);
      } else {
        return $this->redirect(['/user-place']);
      }
    }

    /**
     * Updates an existing Place model.
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
     * Deletes an existing Place model.
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
     * Creates a new Place model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      if (!Place::withinLimit(Yii::$app->user->getId())) {
        Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of places that you can add today. Contact support if you need additional help or want to offer feedback.'));
        return $this->redirect('yours');
      }
        $model = new Place();
        if ($model->load(Yii::$app->request->post())) {
			      $form = Yii::$app->request->post();
            if (!is_numeric($model->place_type)) {
               $model->place_type=Place::TYPE_OTHER;
            }
            $model->created_by= Yii::$app->user->getId();
            // validate the form against model rules
            if ($model->validate()) {
                // all inputs are valid
                $model->save();
                // lookup gps location from address
                // to do - reactivate location add - geotodo
                // $model->addLocationFromAddress($model,$form['Place']['full_address']);
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // validation failed
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
      * Creates a new Place model from Google Place
      * If creation is successful, the browser will be redirected to the 'view' page.
      * @return mixed
      */
     public function actionCreate_place_google()
     {
       if (!Place::withinLimit(Yii::$app->user->getId())) {
         Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of places that you can add today. Contact support if you need additional help or want to offer feedback.'));
         return $this->redirect('yours');
       }

       $model = new Place();
       if ($model->load(Yii::$app->request->post())) {
           $form = Yii::$app->request->post();
           if (!is_numeric($model->place_type)) {
              $model->place_type=Place::TYPE_OTHER;
           }
           $model->created_by= Yii::$app->user->getId();
           // validate the form against model rules
           if ($model->validate()) {
               // all inputs are valid
               $model->save();
               // add GPS entry in PlaceGeometry
               $model->addGeometry($model,$form['Place']['location']);
               return $this->redirect(['view', 'id' => $model->id]);
           } else {
               // validation failed
               return $this->render('create_place_google', [
                   'model' => $model,
               ]);
           }
       } else {
           return $this->render('create_place_google', [
               'model' => $model,
           ]);
       }
     }

     /**
      * Creates a new Place model via Geolocation
      */
     public function actionCreate_geo()
     {
       if (!Place::withinLimit(Yii::$app->user->getId())) {
         Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, you have reached the maximum number of places that you can add today. Contact support if you need additional help or want to offer feedback.'));
         return $this->redirect('yours');
       }
         $model = new Place();
         if ($model->load(Yii::$app->request->post())) {
             $form = Yii::$app->request->post();
             $model->created_by= Yii::$app->user->getId();
             if (!is_numeric($model->place_type)) {
                $model->place_type=Place::TYPE_OTHER;
             }
             if (empty($form['Place']['lat'])) {
               Yii::$app->getSession()->setFlash('error', Yii::t('frontend','Sorry, GPS coordinates could not be determined. Try creating ').Html::a(Yii::t('frontend','a place from here'),['place/create']).'.');
               // validation failed
               return $this->render('create_geo', [
                   'model' => $model,
               ]);
             } else {
               if (!is_null($model->gps) && $model->validate()) {
                    // all inputs are valid
                    $model->save();
                    // add GPS entry in PlaceGeometry
                    $model->addGeometryByPoint($model,$form['Place']['lat'],$form['Place']['lng']);
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    // validation failed
                    return $this->render('create_geo', [
                        'model' => $model,
                    ]);
                }
                }
         } else {
             return $this->render('create_geo', [
                 'model' => $model,
             ]);
         }
     }

     /**
      * Finds the Place model based on its primary key value.
      * If the model is not found, a 404 HTTP exception will be thrown.
      * @param integer $id
      * @return Place the loaded model
      * @throws NotFoundHttpException if the model cannot be found
      */
     protected function findModel($id)
     {
         if (($model = Place::findOne($id)) !== null) {
             return $model;
         } else {
             throw new NotFoundHttpException('The requested page does not exist.');
         }
     }
}
