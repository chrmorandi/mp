<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserProfile */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="col-md-8">
 <!-- Nav tabs -->
 <ul class="nav nav-tabs" role="tablist">
   <li class="active"><a href="#profile" role="tab" data-toggle="tab"><?= Yii::t('frontend','Your name') ?></a></li>
   <li><a href="#social" role="tab" data-toggle="tab"><?= Yii::t('frontend','Link Social accounts') ?></a></li>
 </ul>
 <!-- Tab panes -->
 <div class="tab-content">
   <div class="tab-pane active vertical-pad" id="profile">
     <div class="user-profile-form">

         <?php $form = ActiveForm::begin(); ?>


         <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

         <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

         <?= $form->field($model, 'fullname')->textInput(['maxlength' => true])->label(Yii::t('frontend','Friendly Name'))->hint(Yii::t('frontend','Optional')) ?>

         <?php /* = $form->field($model, 'filename')->textInput(['maxlength' => true]) */ ?>

         <?php /* = $form->field($model, 'avatar')->textInput(['maxlength' => true]) */ ?>


         <div class="form-group">
             <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
         </div>

         <?php ActiveForm::end(); ?>

     </div>

   </div> <!-- end of profile tab -->
    <div class="tab-pane vertical-pad" id="social">
            <p>Do you want to login with one click with one of the following services?</p>
            <?= yii\authclient\widgets\AuthChoice::widget([
                 'baseAuthUrl' => ['site/auth'],
                 'popupMode' => false,
            ]) ?>


    </div> <!-- end of social tab -->
</div>
</div>
