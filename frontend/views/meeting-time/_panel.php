<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use \kartik\switchinput\SwitchInput;
?>
<div id="notifierTime" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><div class="row"><div class="col-lg-10 col-md-10 col-xs-10"><h4><?= Yii::t('frontend','When') ?></h4><p><em>
    <?php if ($timeProvider->count>1) { ?>
      Use switches below to denote acceptable times.&nbsp;
    <?php
      }
    ?>
    <?php if ($timeProvider->count>1 && ($isOwner || $model->meetingSettings['participant_choose_date_time'])) { ?>
      You can also choose the time.
    <?php }?>
  </em></p></div><div class="col-lg-2 col-md-2 col-xs-2"><div style="float:right;">
    <?php
      if ($isOwner || $model->meetingSettings->participant_add_date_time) {
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
           'viewParams' => ['timezone'=>$timezone,'timeCount'=>$timeProvider->count,'isOwner'=>$isOwner,'participant_choose_date_time'=>$model->meetingSettings['participant_choose_date_time'],'whenStatus'=>$whenStatus],
       ]) ?>
  </table>
  <?php else: ?>
  <?php endif; ?>
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
            'pluginOptions' => ['size' => 'mini','onText' => 'acceptable','offText'=>'reject','onColor' => 'success','offColor' => 'danger',],
            ]);
      }
    }
  }

  function showTimeParticipantStatus($model,$isOwner) {
    foreach ($model->meetingTimeChoices as $mtc) {
      if (count($model->meeting->participants)==0) break;
      if ($mtc->user_id == $model->meeting->participants[0]->participant_id) {
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
            'pluginOptions' => ['size'=>'mini','onText' => 'acceptable','offText'=>'reject','onColor' => 'success','offColor' => 'danger',],
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
