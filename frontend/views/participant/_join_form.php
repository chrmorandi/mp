<?php
use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;
?>
 <?php
 $form = ActiveForm::begin();
?>
<p>Do you want to join the meeting using one of the following services?</p>
<?= yii\authclient\widgets\AuthChoice::widget([
     'baseAuthUrl' => ['site/auth'],
     'popupMode' => false,
]) ?>
<p>Or, you can join the meeting with the form below:</p>
 <div class="participant-join-form">
     <?= $form->field($model, 'email')->textInput()->label(Yii::t('frontend','Email Address')) ?>
     <?= $form->field($model, 'firstname')->textInput()->label(Yii::t('frontend','First Name')) ?>
     <?= $form->field($model, 'lastname')->textInput()->label(Yii::t('frontend','Last Name')) ?>

</div>
<div class="form-group">
    <?= Html::submitButton(Yii::t('frontend', 'Join the Meeting'), ['class' => 'btn btn-primary']) ?>
</div>
<?php
ActiveForm::end();
?>
