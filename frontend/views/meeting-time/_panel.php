<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
use \kartik\switchinput\SwitchInput;
?>
<div id="notifierTime" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingWhen">
    <div class="row"><div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhen" aria-expanded="true" aria-controls="collapseWhen"><?= Yii::t('frontend','When') ?></a></h4>
    <span class="hint-text">
      <?php if ($timeProvider->count<=1) { ?>
        <?= Yii::t('frontend','add one or more dates and times for participants to choose from') ?>
    <?php } elseif ($timeProvider->count>1) { ?>
      <?= Yii::t('frontend','are listed times okay?'); ?>
    <?php
      }
    ?>
    <?php if ($timeProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_date_time'])) { ?>
      <?= Yii::t('frontend','you can also choose the time') ?>
    <?php }?>
  </span></div><div class="col-lg-2 col-md-2 col-xs-2"><div style="float:right;">
    <?php
      if ($model->isOrganizer() || $model->meetingSettings->participant_add_date_time) {
        /*echo Html::a('', 'javascript:function ajax() {return false;}', ['class' => 'btn btn-primary  glyphicon glyphicon-plus','id'=>'buttonTime']);*/
        echo Html::a('', ['meeting-time/create', 'meeting_id' => $model->id], ['class' => 'btn btn-primary  glyphicon glyphicon-plus','id'=>'buttonTime']);
      }
    ?>
      </div>
    </div>
  </div> <!-- end row -->
</div> <!-- end heading -->
  <div id="addTime" style="display:none;">
    <!-- hidden add time form -->
  <br />
  </div>
  <div id="collapseWhen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhen">
    <div class="panel-body">
  <?php
   if ($timeProvider->count>0):
  ?>
  <!-- Table -->
  <table class="table">
    <?= ListView::widget([
           'dataProvider' => $timeProvider,
           'itemOptions' => ['class' => 'item'],
           'layout' => '{items}',
           'itemView' => '_list',
           'viewParams' => ['timezone'=>$timezone,'timeCount'=>$timeProvider->count,'isOwner'=>$model->isOrganizer(),'participant_choose_date_time'=>$model->meetingSettings['participant_choose_date_time'],'whenStatus'=>$whenStatus],
       ]) ?>
  </table>
  <?php else: ?>
  <?php endif; ?>
  </div>
  </div>
</div>

<?php

  function showTimeOwnerStatus($model,$isOwner) {
    foreach ($model->meetingTimeChoices as $mtc) {
      if ($mtc->user_id == $model->meeting->owner_id) {
          if ($mtc->status == $mtc::STATUS_YES)
            $value = 1;
          else
            $value =0;
            echo SwitchInput::widget([
            'type' => SwitchInput::CHECKBOX,
            'name' => 'meeting-time-choice',
            'id'=>'mtc-'.$mtc->id,
            'value' => $value,
            'disabled' => !$isOwner,
            'pluginOptions' => ['size' => 'small','handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no','onColor' => 'success','offColor' => 'danger',],
            ]);
      }
    }
  }

  function showTimeParticipantStatus($model,$isOwner,$user_id) {
    foreach ($model->meetingTimeChoices as $mtc) {
      if (count($model->meeting->participants)==0) break;
      if ($mtc->user_id == $user_id) {
          if ($mtc->status == $mtc::STATUS_YES)
            $value = 1;
          else if ($mtc->status == $mtc::STATUS_NO)
            $value =0;
          else if ($mtc->status == $mtc::STATUS_UNKNOWN)
            $value =-1;
          echo SwitchInput::widget([
            'type' => SwitchInput::CHECKBOX,
            'name' => 'meeting-time-choice',
            'id'=>'mtc-'.$mtc->id,
            'tristate'=>true,
            'indeterminateValue'=>-1,
            'indeterminateToggle'=>false,
            'disabled'=>$isOwner,
            'value' => $value,
            'pluginOptions' => ['size' => 'small','handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no','onColor' => 'success','offColor' => 'danger',],
        ]);
      }
    }
  }
?>
<?php
$urlPrefix = \common\components\MiscHelpers::getUrlPrefix();
$script = <<< JS
timeCount = $timeProvider->count;
$('input[name="time-chooser"]').on('switchChange.bootstrapSwitch', function(e, s) {
  //console.log(e.target.value); // true | false
  $.ajax({
     url: '$urlPrefix/meeting-time/choose',
     data: {id: $model->id, 'val': e.target.value},
     // e.target.value is selected MeetingTimeChoice model
     success: function(data) {
       displayNotifier('time');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// users can say if a time is an option for them
$('input[name="meeting-time-choice"]').on('switchChange.bootstrapSwitch', function(e, s) {
  // set intval to pass via AJAX from boolean state
  if (s)
    state = 1;
  else
    state =0;
  $.ajax({
     url: '$urlPrefix/meeting-time-choice/set',
     data: {id: e.target.id, 'state': state},
     success: function(data) {
       displayNotifier('time');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});
/*
$('#buttonTime').on('click', function(e, s) {
  //console.log(e.target.value); // true | false
  $.ajax({
     url: '$urlPrefix/meeting-time/create',
     data: {id: $model->id},
     success: function(data) {

       //$('#addTime').html(data);
       //$('#addTime').className='';
       $('#addTime').show();
       return true;
     }
  });
});
*/
JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);
?>
