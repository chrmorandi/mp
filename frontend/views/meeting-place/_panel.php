<?php
use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div id="notifierPlace" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-9"><h4><?= Yii::t('frontend','Places') ?></h4><p><em>
        <?php if ($placeProvider->count>1) { ?>
          Use the switches below to indicate which places are acceptable options for you.&nbsp;
        <?php
          }
        ?>
        <?php if ($placeProvider->count>1 && ($isOwner || $model->meetingSettings['participant_choose_place'])) { ?>
          You are also allowed to choose the meeting place.
        <?php }?>
      </em></p></div>

<?php
  if (!$isOwner) {
    // To Do: Check Meeting Settings whether participant can add places
  }
?>
      <div class="col-lg-3" ><div style="float:right;">
        <?php
          if ($isOwner || $model->meetingSettings->participant_add_place) {
            echo Html::a('', ['meeting-place/create', 'meeting_id' => $model->id], ['class' => 'btn btn-primary glyphicon glyphicon-plus']);
          }
        ?>
              </div>
    </div>
  </div>
  </div>

  <?php
   if ($placeProvider->count>0):
  ?>
  <table class="table">
     <thead>
     <tr class="small-header">
       <td></td>
       <td ><?=Yii::t('frontend','You') ?></td>
       <td ><?=Yii::t('frontend','Them') ?></td>
        <td >
          <?php
           if ($placeProvider->count>1 && ($isOwner || $model->meetingSettings['participant_choose_place'])) echo Yii::t('frontend','Choose');
          ?></td>
    </tr>
    </thead>
    <?= ListView::widget([
           'dataProvider' => $placeProvider,
           'itemOptions' => ['class' => 'item'],
           'layout' => '{items}',
           'itemView' => '_list',
           'viewParams' => ['placeCount'=>$placeProvider->count,'isOwner'=>$isOwner,'participant_choose_place'=>$model->meetingSettings['participant_choose_place']],
       ]) ?>
  </table>
  <?php else: ?>
  <?php endif; ?>

</div>
<?php
if (isset(Yii::$app->params['urlPrefix'])) {
  $urlPrefix = Yii::$app->params['urlPrefix'];
  } else {
    $urlPrefix ='';
  }
$script = <<< JS
placeCount = $placeProvider->count;
// allows user to set the final place
$('input[name="place-chooser"]').on('switchChange.bootstrapSwitch', function(e, s) {
  // console.log(e.target.value); // true | false
  // turn on mpc for user
  $.ajax({
     url: '$urlPrefix/meeting-place/choose',
     data: {id: $model->id, 'val': e.target.value},
     // e.target.value is selected MeetingPlaceChoice model
     success: function(data) {
       displayNotifier('place');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

// users can say if a place is an option for them
$('input[name="meeting-place-choice"]').on('switchChange.bootstrapSwitch', function(e, s) {
  //console.log(e.target.id,s); // true | false
  // set intval to pass via AJAX from boolean state
  if (s)
    state = 1;
  else
    state =0;
  $.ajax({
     url: '$urlPrefix/meeting-place-choice/set',
     data: {id: e.target.id, 'state': state},
     success: function(data) {
       displayNotifier('place');
       refreshSend();
       refreshFinalize();
       return true;
     }
  });
});

JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);
?>

<?php
use \kartik\switchinput\SwitchInput;

  function showOwnerStatus($model,$isOwner) {
    foreach ($model->meetingPlaceChoices as $mpc) {
      if ($mpc->user_id == $model->meeting->owner_id) {
          if ($mpc->status == $mpc::STATUS_YES)
            $value = 1;
          else
            $value =0;
          echo SwitchInput::widget([
          'type'=>SwitchInput::CHECKBOX,
          'name' => 'meeting-place-choice',
          'id'=>'mpc-'.$mpc->id,
          'value' => $value,
          'disabled' => !$isOwner,
          'pluginOptions' => ['size' => 'mini','onText' => 'okay','offText'=>'reject','onColor' => 'success','offColor' => 'danger',],
          ]);
      }
    }
  }

  function showParticipantStatus($model,$isOwner) {
    foreach ($model->meetingPlaceChoices as $mpc) {
      if (count($model->meeting->participants)==0) break;
      if ($mpc->user_id == $model->meeting->participants[0]->participant_id) {
          if ($mpc->status == $mpc::STATUS_YES)
            $value = 1;
          else if ($mpc->status == $mpc::STATUS_NO)
            $value =0;
          else if ($mpc->status == $mpc::STATUS_UNKNOWN)
            $value =-1;
          echo SwitchInput::widget([
            'type'=>SwitchInput::CHECKBOX,
            'name' => 'meeting-place-choice',
            'id'=>'mpc-'.$mpc->id,
            'tristate'=>true,
            'indeterminateValue'=>-1,
            'indeterminateToggle'=>false,
            'disabled'=>$isOwner,
            'value' => $value,
            'pluginOptions' => ['size' => 'mini','onText' => 'okay','offText'=>'reject','onColor' => 'success','offColor' => 'danger'],
            // <i class="glyphicon glyphicon-ok"></i>
            // <i class="glyphicon glyphicon-remove"></i>
        ]);
      }
    }
  }
?>
