<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'meeting_type')
            ->dropDownList(
                $model->getMeetingTypeOptions(),
                ['prompt'=>Yii::t('frontend','What type of meeting is this?')]
            )->label(Yii::t('frontend','Meeting Type')) ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => 255])->label(Yii::t('frontend','Subject')) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6])->label(Yii::t('frontend','Message'))->hint(Yii::t('frontend','Optional')) ?>

  

    <?php ActiveForm::end(); ?>

</div>
