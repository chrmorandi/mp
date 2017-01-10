<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\typeahead\TypeaheadBasic;
/* @var $this yii\web\View */
/* @var $model frontend\models\Template */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


    <?php
    echo $form->field($model, 'subject')->widget(TypeaheadBasic::className(), [
    'data' => $subjects,
    'options' => ['placeholder' => Yii::t('frontend','what\'s a subject you would like to use?'),
    ],
    'pluginOptions' => ['highlight'=>true],
    ]);
    ?>

    <div class="itemHide">
    <?= $form->field($model, 'message')->textarea(['rows' => 6])->label(Yii::t('frontend','Message'))->hint(Yii::t('frontend','Optional')) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
