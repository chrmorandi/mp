<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingTime */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-time-form">

  <div class="row">
    <div class="col-md-4">
    <?php $form = ActiveForm::begin(); ?>

    <?= DateTimePicker::widget([
        'model' => $model,
        'attribute' => 'start',
        'language' => 'en',
        'size' => 'ms',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'MM d, yyyy H:ii p',
            'todayBtn' => false,
            'minuteStep'=> 15,
            'showMeridian'=>true,
            //'pickerPosition' => 'bottom-left',
            //'startView'=>2,
            // to do - format one day ahead
            'initialDate'=> Date('Y-m-d',time()+3600*72),
        ]
    ]);?>
    </div>
  </div>
  <div class="clearfix"><p></div>
  <div class="row">
      <div class="col-md-4">
     <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Add') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    </div>
  </div>
    <?php ActiveForm::end(); ?>

</div>
