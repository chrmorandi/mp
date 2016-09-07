<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Launch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="launch-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-lg-6">
    <div class="input-group">
      <input type="text" class="form-control" placeholder="email address">
          <span class="input-group-btn">
            <button class="btn btn-success" type="button"><?= Yii::t('frontend','Submit') ?></button>
          </span>
      </div><!-- /input-group -->
</div>
    <?php ActiveForm::end(); ?>

</div>
