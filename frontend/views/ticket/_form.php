<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'subject')->textInput()->label(Yii::t('frontend','What\'s your question about?')) ?>
    <?= $form->field($model, 'details')->label(Yii::t('frontend','Tell us more about your question'))->textarea(['rows' => 6]) ?>
    <?php
      if (Yii::$app->user->isGuest) {
       echo $form->field($model, 'email')->textInput()->label(Yii::t('frontend','Email address'))->hint(Yii::t('frontend','this will allow us to answer your questions'));
      }
     ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
