<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'meeting_id')->textInput() ?>

    <?= $form->field($model, 'participant_add_place')->textInput() ?>

    <?= $form->field($model, 'participant_add_date_time')->textInput() ?>

    <?= $form->field($model, 'participant_choose_place')->textInput() ?>

    <?= $form->field($model, 'participant_choose_date_time')->textInput() ?>

    <?= $form->field($model, 'participant_finalize')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
