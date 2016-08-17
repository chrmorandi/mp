<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RequestResponse */
/* @var $form yii\widgets\ActiveForm */
?>

<p><em>
<?= $subject ?>
</em>
</p>
<div class="request-response-form">

    <?php $form = ActiveForm::begin(); ?>
      <?= BaseHtml::activeHiddenInput($model, 'responder_id'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'request_id'); ?>
    <?= $form->field($model, 'note')->label(Yii::t('frontend','Include a note'))->textarea(['rows' => 6])->hint(Yii::t('frontend','optional')) ?>

<div class="form-group">
  <?= Html::submitButton(Yii::t('frontend', 'Accept Request'), ['class' => 'btn btn-success','name'=>'accept',]) ?>
  <?= Html::submitButton(Yii::t('frontend', 'Decline Request'),['class' => 'btn btn-danger','name'=>'reject',
      'data' => [
          'confirm' => Yii::t('frontend', 'Are you sure you want to decline this request?'),
          'method' => 'post',
      ],]) ?>



    </div>

    <?php ActiveForm::end(); ?>

</div>
