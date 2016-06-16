<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingNote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6])->label(Yii::t('frontend','What would you like to add?')); ?>

    <div class="form-group">
      <span class="button-pad">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
      </span>
      <span class="button-pad">
        <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view', 'id' => $model->meeting_id], ['class' => 'btn btn-danger']) ?>
      </span>
    </div>

    <?php ActiveForm::end(); ?>

</div>
