<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ticket */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ticket-form">    

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question')->label(Yii::t('frontend','How can we help you?'))->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
