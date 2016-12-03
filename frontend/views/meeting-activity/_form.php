<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\MeetingActivity;
use common\components\MiscHelpers;
use \kartik\typeahead\TypeaheadBasic;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingActivity */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="meeting-activity-form">
  <div class="row">
    <div class="col-xs-12 col-md-8 col-lg-8">
    <?php $form = ActiveForm::begin();?>
    <?= Html::activeHiddenInput($model, 'url_prefix',['value'=>MiscHelpers::getUrlPrefix(),'id'=>'url_prefix']); ?>
    <?php
//    id="meetingplace-place_id" name="MeetingPlace[place_id]">
    $activities=MeetingActivity::defaultActivityList();

      ?>
      <p><strong><?= Yii::t('frontend','Suggest an activity');?></strong></p>
<select class="combobox input-large form-control" id="meeting_activity" name="Meeting[activity]">
<option value="" selected="selected"><?= Yii::t('frontend','type or click at right to see suggestions or suggest a different one')?></option>
  <?php
  foreach ($activities as $activity) {
    ?>
    <option value="<?= $activity;?>"><?= $activity;?></option>
    <?php
  }
  ?>
</select>
  </div>
  </div>
  <div class="clearfix"><p></div>
  <div class="row">
      <div class="col-xs-12 col-md-12 col-lg-12">
     <div class="form-group">
       <span class="button-pad">
         <?= Html::a(Yii::t('frontend','Add Meeting Activity'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addActivity('.$model->meeting_id.');'])  ?>
       </span><span class="button-pad">
         <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelActivity();'])  ?>
      </span>
     </div>
    </div>
  </div>
  <?php ActiveForm::end();
   ?>

</div> <!-- end container -->
