<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Reminder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-form">

    <?php $form = ActiveForm::begin(); ?>


    <? //= $form->field($model, 'unit')->textInput() ?>

    <? //= $form->field($model, 'duration_friendly')->textInput() ?>

    <?= $form->field($model, 'reminder_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
