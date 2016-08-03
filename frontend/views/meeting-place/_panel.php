<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use \kartik\switchinput\SwitchInput;

?>
<div id="notifierPlace" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-place"><?= Yii::t('frontend','Where') ?></h4><p><em>
        <span class="hint-text">
        <?php if ($placeProvider->count<=1) { ?>
          <?= Yii::t('frontend','add places for participants or switch to \'virtual\'') ?>
      <?php } elseif ($placeProvider->count>1) { ?>
          <?= Yii::t('frontend','accept or reject places below&nbsp;') ?>
        <?php
          }
        ?>
        <?php if ($placeProvider->count>1 && ($isOwner || $model->meetingSettings['participant_choose_place'])) { ?>
          <?= Yii::t('frontend','- you can also choose the place') ?>
        <?php }?>
      </span></div>

<?php
  if (!$isOwner) {
    // To Do: Check Meeting Settings whether participant can add places
  }
?>
      <div class="col-lg-2 col-md-2 col-xs-2" ><div style="float:right;">
        <?php
          if ($isOwner || $model->meetingSettings->participant_add_place) {
          ?>
          <table><tr style="vertical-align:top;"><td class="virtualThing" style="padding-left:10px;">
            <?php
            echo SwitchInput::widget([
              'type' => SwitchInput::CHECKBOX,
              'name' => 'meeting-switch-virtual',
                'value' => $model->switchVirtual,
                'pluginOptions' => [
                  'handleWidth'=>75,
                  'size'=>'mini','onText' => '<i class="glyphicon glyphicon-user"></i>&nbsp;in person','offText'=>'<i class="glyphicon glyphicon-earphone"></i>&nbsp;virtual'], // 'onColor' => 'success','offColor' => 'danger'
                'labelOptions' => ['style' => 'font-size: 8px;'],
            ]);
            ?>
            </td><td style="padding-left:10px;">
            <?php
              if ($model->switchVirtual == $model::SWITCH_INPERSON) {
                  echo Html::a('', ['meeting-place/create', 'meeting_id' => $model->id], ['id'=>'meeting-add-place','class' => 'btn btn-primary glyphicon glyphicon-plus']);
              } else {
                echo Html::a('', 'javascript:void(0);', ['id'=>'meeting-add-place','class' => 'btn btn-primary glyphicon glyphicon-plus','disabled'=>true]);
              }

            ?>
            </td></tr></table>
          <?php
          }
        ?>
              </div>
    </div>
  </div>
  </div>
  <?php
    $style = ($model->switchVirtual==$model::SWITCH_VIRTUAL?'none':'block');
   ?>
  <div id ="meeting-place-list" style="display:<?php echo $style; ?>">
  <?php
   if ($placeProvider->count>0):
  ?>
  <table class="table">
    <?= ListView::widget([
           'dataProvider' => $placeProvider,
           'itemOptions' => ['class' => 'item'],
           'layout' => '{items}',
           'itemView' => '_list',
           'viewParams' => ['placeCount'=>$placeProvider->count,'isOwner'=>$isOwner,'participant_choose_place'=>$model->meetingSettings['participant_choose_place'],'whereStatus'=>$whereStatus],
       ]) ?>
  </table>
  <?php else: ?>
  <?php endif; ?>
</div>

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

// users can say if a place is an option for them
$('input[name="meeting-switch-virtual"]').on('switchChange.bootstrapSwitch', function(e, s) {
  //console.log(e.target.id,s); // true | false
  // set intval to pass via AJAX from boolean state

  if (!s) {
    // change to virtual
    $('#meeting-add-place').prop("disabled",true);
    $('a#meeting-add-place').attr('disabled', true);
    $('a#meeting-add-place').prop('href', 'javascript:void(0);');
    $('#meeting-place-list').prop('style','display:none;');
    state = 1; // state of these are backwards: true is 0, 1 is false
  } else {
    // change to in person
    $('#meeting-add-place').prop("disabled",false);
    $('a#meeting-add-place').attr('disabled', false);
    $('a#meeting-add-place').prop('href', '$urlPrefix/meeting-place/create/?meeting_id=$model->id');
    $('#meeting-place-list').prop('style','display:block;');
    state =0; // state of these are backwards: true is 0, 1 is false
  }
  $.ajax({
     url: '$urlPrefix/meeting/virtual',
     data: {id: $model->id, 'state': state},
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
          'pluginOptions' => ['size' => 'mini','handleWidth'=>75,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;acceptable','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;reject','onColor' => 'success','offColor' => 'danger',],
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
            'pluginOptions' => ['size' => 'mini','handleWidth'=>75,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;acceptable','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;reject','onColor' => 'success','offColor' => 'danger',],
        ]);
      }
    }
  }
?>
