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
<?php
$activities=MeetingActivity::defaultActivityList();
echo $form->field($model, 'activity')->label(Yii::t('frontend','Suggest an activity'))->widget(TypeaheadBasic::className(), [
'data' => $activities,
'options' => ['placeholder' => Yii::t('frontend','enter your suggestions'),
'id'=>'meeting_activity',
  //'class'=>'input-large form-control'
],
'pluginOptions' => ['highlight'=>true],
]);
?>
  </div>
  </div>
  <div class="clearfix"><p></div>
  <div class="row">
      <div class="col-xs-12 col-md-12 col-lg-12">
     <div class="form-group">
       <span class="button-pad">
         <?= Html::a(Yii::t('frontend','Add an activity'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addActivity('.$model->meeting_id.');'])  ?>
       </span><span class="button-pad">
         <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelActivity();'])  ?>
      </span>
     </div>
    </div>
  </div>
  <?php ActiveForm::end();
   ?>

</div> <!-- end container -->
