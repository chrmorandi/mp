<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HistoricalData */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="historical-data-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'count_users')->textInput() ?>

    <?= $form->field($model, 'count_meetings_completed')->textInput() ?>
     <?= $form->field($model, 'count_meetings_expired')->textInput() ?> 

    <?= $form->field($model, 'count_meetings_planning')->textInput() ?>

    <?= $form->field($model, 'count_places')->textInput() ?>

    <?= $form->field($model, 'average_meetings')->textInput() ?>

    <?= $form->field($model, 'average_friends')->textInput() ?>

    <?= $form->field($model, 'average_places')->textInput() ?>

    <?= $form->field($model, 'source_google')->textInput() ?>

    <?= $form->field($model, 'source_facebook')->textInput() ?>

    <?= $form->field($model, 'source_linkedin')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('backend', 'Create') : Yii::t('backend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
