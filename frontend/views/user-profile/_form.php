<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserProfile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="col-md-8">
 <!-- Nav tabs -->
 <ul class="nav nav-tabs" role="tablist">
   <li class="active"><a href="#profile" role="tab" data-toggle="tab"><?= Yii::t('frontend','Your name') ?></a></li>
   <li><a href="#social" role="tab" data-toggle="tab"><?= Yii::t('frontend','Link Social accounts') ?></a></li>
   <li><a href="#photo" role="tab" data-toggle="tab"><?= Yii::t('frontend','Upload Photo') ?></a></li>
 </ul>
 <!-- Tab panes -->
 <?php
 $form = ActiveForm::begin([
     'options'=>['enctype'=>'multipart/form-data']]); // important
      ?>
 <div class="tab-content">
   <div class="tab-pane active vertical-pad" id="profile">
     <div class="user-profile-form">
         <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>
         <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>
         <?= $form->field($model, 'fullname')->textInput(['maxlength' => true])->label(Yii::t('frontend','Friendly Name'))->hint(Yii::t('frontend','Optional')) ?>
         <div class="form-group">
             <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         </div>

     </div>

   </div> <!-- end of profile tab -->
    <div class="tab-pane vertical-pad" id="social">
            <p>Do you want to login with one click with one of the following services?</p>
            <?= yii\authclient\widgets\AuthChoice::widget([
                 'baseAuthUrl' => ['site/auth'],
                 'popupMode' => false,
            ]) ?>
    </div> <!-- end of social tab -->
    <div class="tab-pane vertical-pad" id="photo">
      <?=$form->field($model, 'image')->widget(FileInput::classname(), [
          'options' => ['accept' => 'image/*','data-show-upload'=>'false'],
           'pluginOptions'=>['allowedFileExtensions'=>['jpg','gif','png']],
      ]);   ?>
      <div class="form-group">
          <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
      </div>

    </div> <!-- end of upload photo tab -->
</div> <!-- end tab content -->
<?php ActiveForm::end(); ?>
</div> <!-- end left col -->
<div class="col-md-4">
  <?php
   if ($model->avatar<>'') {
     echo '<img src="'.Yii::getAlias('@web').'/uploads/avatar/sqr_'.$model->avatar.'" class="profile-image"/>';
   } else {
     echo \cebe\gravatar\Gravatar::widget([
          'email' => common\models\User::find()->where(['id'=>Yii::$app->user->getId()])->one()->email,
          'options' => [
              'class'=>'profile-image',
              'alt' => common\models\User::find()->where(['id'=>Yii::$app->user->getId()])->one()->username,
          ],
          'size' => 128,
      ]);
   }
  ?>
</div> <!--end rt col -->
