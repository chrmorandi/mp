<?php
use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;
?>
<?php
 $form = ActiveForm::begin();
?>
<div class="row">
  <div class="col-xs-12 col-lg-6">
<p><?= Yii::t('frontend','You can connect with any one of the following services:');?></p>
<?= yii\authclient\widgets\AuthChoice::widget([
     'baseAuthUrl' => ['site/auth'],
     'popupMode' => false,
]) ?>

<p><?= Yii::t('frontend','Or, you can use the form below:');?></p>
 <div class="participant-join-form">
     <?= $form->field($model, 'firstname')->textInput()->label(Yii::t('frontend','First Name')) ?>
     <?= $form->field($model, 'lastname')->textInput()->label(Yii::t('frontend','Last Name')) ?>
     <?= $form->field($model, 'email')->textInput()->label(Yii::t('frontend','Email Address')) ?>
</div>

<div class="form-group">
    <?= Html::submitButton(Yii::t('frontend', 'Join the Meeting'), ['class' => 'btn btn-primary']) ?>
</div>
</div> <!-- end col-->
</div> <!-- end row -->
<?php
ActiveForm::end();
?>
