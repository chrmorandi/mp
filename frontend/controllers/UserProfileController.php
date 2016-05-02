<?php

namespace frontend\controllers;

use Yii;
use frontend\models\UserProfile;
use frontend\models\UserProfileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\imagine\Image;

/**
 * UserProfileController implements the CRUD actions for UserProfile model.
 */
class UserProfileController extends Controller
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
        ];
    }

    /**
     * Lists all UserProfile models.
     * @return mixed
     */
    public function actionIndex()
    {
      // returns record id not user_id
      //echo Yii::$app->user->getId();
      $id = UserProfile::initialize(Yii::$app->user->getId());
      return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Updates an existing UserProfile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
      // the path to save file, you can set an uploadPath
      // in Yii::$app->params (as used in example below)
      Yii::$app->params['uploadPath'] = Yii::$app->basePath . '/web/uploads/avatar/';
      $model = $this->findModel($id);
      if (is_null($model)) {
        // create the user profile for this User if it doesn't exist
        $up_id = UserProfile::initialize(Yii::$app->user->getId());
        $model=$this->findOne($up_id);
      } else {
          $model->user_id = Yii::$app->user->getId();
      }
      if ($model->load(Yii::$app->request->post())) {
        $image = UploadedFile::getInstance($model, 'image');
        if (!is_null($image)) {
           // path to existing image for post-delete
           $image_delete = $model->avatar;
           // save new image
            // store the source file name
           $model->filename = $image->name;
           $ext = end((explode(".", $image->name)));
           // generate a unique file name to prevent duplicate filenames
           $model->avatar = Yii::$app->security->generateRandomString().".{$ext}";
           if($model->save()){
             $path = Yii::$app->params['uploadPath'] . $model->avatar;
             $results = $image->saveAs($path);
             Image::thumbnail(Yii::$app->params['uploadPath'].$model->avatar, 120, 120)
                 ->save(Yii::$app->params['uploadPath'].'sqr_'.$model->avatar, ['quality' => 50]);
             Image::thumbnail(Yii::$app->params['uploadPath'].$model->avatar, 30, 30)
                     ->save(Yii::$app->params['uploadPath'].'sm_'.$model->avatar, ['quality' => 50]);
              if (file_exists(Yii::$app->params['uploadPath'].$image_delete)) {
                $model->deleteImage(Yii::$app->params['uploadPath'],$image_delete);
              }
           } else {
             // error in saving model
             // pass thru to form
           }
         } else {
           // simple save
           Yii::$app->getSession()->setFlash('success', Yii::t('frontend','Your profile has been updated.'));
           $model->update();
           // pass thru to form
         }
      }
      return $this->render('update', [
          'model' => $model,
      ]);
    }

    /**
     * Finds the UserProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserProfile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
