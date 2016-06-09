<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'is_social')->textInput() ?>

    <?= $form->field($model, 'invite_then_own')->textInput() ?>

    <?= $form->field($model, 'count_meetings')->textInput() ?>

    <?= $form->field($model, 'count_meetings_last30')->textInput() ?>

    <?= $form->field($model, 'count_meeting_participant')->textInput() ?>

    <?= $form->field($model, 'count_meeting_participant_last30')->textInput() ?>

    <?= $form->field($model, 'count_places')->textInput() ?>

    <?= $form->field($model, 'count_friends')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
