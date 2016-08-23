<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;
use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingTime */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tz_success" id="tz_success">
<div id="w4-tz-success" class="alert-success alert fade in">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::t('frontend','Your timezone has been updated successfully.') ?>
</div>
</div>
<div class="tz_warning" id="tz_alert">
<div id="w4-tz-info" class="alert-info alert fade in">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::t('frontend','Would you like us to change your timezone setting to <span id="tz_new"></span>?') ?>
</div>
</div>
<div class="meeting-time-form">

  <div class="row">
    <div class="col-xs-8 col-lg-4">
    <?php $form = ActiveForm::begin();?>
    <?= BaseHtml::activeHiddenInput($model, 'url_prefix',['value'=>MiscHelpers::getUrlPrefix(),'id'=>'url_prefix']); ?>
    <?= BaseHtml::activeHiddenInput($model, 'tz_dynamic',['id'=>'tz_dynamic']); ?>
    <?= BaseHtml::activeHiddenInput($model, 'tz_current',['id'=>'tz_current']); ?>
    <strong><?php echo Yii::t('frontend','Date') ?></strong>
    <?= DateTimePicker::widget([
        'model' => $model,
        'attribute' => 'start',
        //'language' => 'en',
        'size' => 'ms',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'MM d, yyyy',
            'todayBtn' => true,
            //'pickerPosition' => 'bottom-left',
            'startView'=>2,
            'minView'=>2,
            // to do - format three day ahead
            'initialDate'=> Date('Y-m-d',time()+3600*72),
        ]
    ]);?>
    <p></p>
  </div>
  <div class="col-xs-4 col-lg-8">
  </div>
</div>
<div class="row">
  <div class="col-xs-8 col-lg-4">
    <strong><?php echo Yii::t('frontend','Time') ?></strong>
    <?= DateTimePicker::widget([
        'model' => $model,
        'attribute' => 'start_time',
        //'language' => 'en',
        'size' => 'ms',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'H:ii p',
            'todayBtn' => false,
            'minuteStep'=> 15,
            'showMeridian'=>true,
            //'pickerPosition' => 'bottom-left',
            'startView'=>1,
            'minView'=>0,
            'maxView'=>1,
            // to do - format one day ahead
            //'initialDate'=> Date('Y-m-d'),
            // $( "th.switch" ).text( "Pick the time" );
        ]
    ]);?>
    <p></p>
    </div>
    <div class="col-xs-4 col-lg-8">
    </div>
  </div>
  <div class="row">
    <div class="col-xs-8 col-lg-4">
      <?php
      //$durationList = [1,2,3,4,5,6,12,24,48,72];
      $durationList = [1=>'1 hour',2=>'2 hours',3=>'3 hours',4=>'4 hours',5=>'5 hours',6=>'6 hours',12=>'12 hours',24=>'24 hours',48=>'48 hours',72=>'72 hours'];
      echo $form->field($model, 'duration')
        ->dropDownList(
            $durationList,           // Flat array ('id'=>'label')
            ['prompt'=>'select a duration']    // options
        );
        ?>
        <div class="col-xs-4 col-lg-8">
        </div>
      </div>
  </div>
  <div class="clearfix"><p></div>
  <div class="row">
      <div class="col-xs-12 col-lg-4">
     <div class="form-group">
       <span class="button-pad">
         <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
       </span><span class="button-pad">
        <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view', 'id' => $model->meeting_id], ['class' => 'btn btn-danger']) ?>
      </span>
     </div>
    </div>
  </div>
  <?php ActiveForm::end();
   $this->registerJsFile(MiscHelpers::buildUrl().'/js/jstz.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
   $this->registerJsFile(MiscHelpers::buildUrl().'/js/meeting_time.js',['depends' => [\yii\web\JqueryAsset::className()]]);
   ?>

</div> <!-- end container -->
