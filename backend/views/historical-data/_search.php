<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HistoricalDataSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="historical-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'percent_own_meeting') ?>

    <?= $form->field($model, 'percent_own_meeting_last30') ?>

    <?= $form->field($model, 'percent_invited_own_meeting') ?>

    <?php // echo $form->field($model, 'percent_participant') ?>

    <?php // echo $form->field($model, 'count_users') ?>

    <?php // echo $form->field($model, 'count_meetings_completed') ?>

    <?php // echo $form->field($model, 'count_meetings_planning') ?>

    <?php // echo $form->field($model, 'count_places') ?>

    <?php // echo $form->field($model, 'average_meetings') ?>

    <?php // echo $form->field($model, 'average_friends') ?>

    <?php // echo $form->field($model, 'average_places') ?>

    <?php // echo $form->field($model, 'source_google') ?>

    <?php // echo $form->field($model, 'source_facebook') ?>

    <?php // echo $form->field($model, 'source_linkedin') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
