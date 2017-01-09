<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="place-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model);?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'full_address')->textInput(['maxlength' => 255])->label('Address') ?>

    <?= $form->field($model, 'website')->textInput(['maxlength' => 255,'placeholder'=>'http://']) ?>

    <?= $form->field($model, 'place_type')
            ->dropDownList(
                $model->getPlaceTypeOptions(),
                ['prompt'=>Yii::t('frontend','What type of place is this?')]
            )->label(Yii::t('frontend','Type of Place')) ?>

    <?= $form->field($model, 'notes')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend','Cancel'), ['/place'], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
