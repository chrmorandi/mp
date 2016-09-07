<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingNote */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="meeting-note-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'note')->textarea(['rows' => 6])->label(Yii::t('frontend','What would you like to say?')); ?>
    <div class="form-group">
      <span class="button-pad">
        <?= Html::a(Yii::t('frontend','Submit'), 'javascript:void(0);', ['class' => 'btn btn-primary','onclick'=>'updateNote('.$model->id.');']) ?>
      </span>
      <span class="button-pad">
        <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelNote();']) ?>
      </span>
    </div>
    <?php ActiveForm::end(); ?>
</div>
