<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserDataSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'is_social') ?>

    <?= $form->field($model, 'invite_then_own') ?>

    <?= $form->field($model, 'count_meetings') ?>

    <?php // echo $form->field($model, 'count_meetings_last30') ?>

    <?php // echo $form->field($model, 'count_meeting_participant') ?>

    <?php // echo $form->field($model, 'count_meeting_participant_last30') ?>

    <?php // echo $form->field($model, 'count_places') ?>

    <?php // echo $form->field($model, 'count_friends') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
