<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;

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
    <div class="col-xs-12 col-md-4 col-lg-3">
    <?php $form = ActiveForm::begin();?>
    <?= Html::activeHiddenInput($model, 'url_prefix',['value'=>MiscHelpers::getUrlPrefix(),'id'=>'url_prefix']); ?>
    <strong><?php echo Yii::t('frontend','Date') ?></strong>
    <div class="datetimepicker-width">
      something here
    </div>
    <p></p>
  </div>
  <div class="col-xs-12 col-md-4 col-lg-3">
    <strong><?php echo Yii::t('frontend','Time') ?></strong>
    <div class="datetimepicker-width">
    more
    </div>
    <p></p>
    </div>
    <div class="col-xs-6 col-md-2 col-lg-2">
    dur
    </div>
      <div class="col-xs-6 col-md-2 col-lg-2" style="margin-top:3em;">
      <?= Html::a(Yii::t('frontend','advanced options'),'javascript:void(0);', ['onclick'=>'toggleTimeAdvanced();']);?>
    </div>
  </div>
  <div class="clearfix"><p></div>
  <div class="row">
      <div class="col-xs-12 col-md-12 col-lg-12">
     <div class="form-group">
       <span class="button-pad">
         <?= Html::a(Yii::t('frontend','Add Meeting Time'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addTime('.$model->meeting_id.');'])  ?>
       </span><span class="button-pad">
         <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelTime();'])  ?>
      </span>
     </div>
    </div>
  </div>
  <?php ActiveForm::end();
   ?>

</div> <!-- end container -->
