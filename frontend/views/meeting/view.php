<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */

$this->title = $model->getMeetingHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-view">
  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
      <div class="row">
        <div class="col-lg-12"><h1><?php echo Html::encode($this->title) ?></h1></div>
      </div>
    </div>
    <div class="panel-body">
    <?php echo $model->message.'&nbsp;'; ?>
    </div>
    <div class="panel-footer">
      <div class="row">
        <div class="col-lg-6"></div>
        <div class="col-lg-6" >
          <div style="float:right;">
            <!--  to do - check meeting settings if participant can send/finalize -->
          <?php
          if ($isOwner && $model->status < $model::STATUS_SENT)
           {
          echo Html::a(Yii::t('frontend', 'Send'), ['send', 'id' => $model->id], ['id'=>'actionSend','class' => 'btn btn-primary '.(!$model->isReadyToSend?'disabled':'')]);
          }
        ?>
          <?php
          if (($isOwner  || $model->meetingSettings->participant_finalize) && $model->status<$model::STATUS_CONFIRMED) {
            echo Html::a(Yii::t('frontend', 'Finalize'), ['finalize', 'id' => $model->id], ['id'=>'actionFinalize','class' => 'btn btn-success '.(!$model->isReadyToFinalize?'disabled':'')]);
          }
           ?>
          <?php
            if ($isOwner) {
              echo Html::a('', ['cancel', 'id' => $model->id],
               ['class' => 'btn btn-primary glyphicon glyphicon-remove-circle btn-danger',
               'title'=>Yii::t('frontend','Cancel'),
               'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
              ]);
            } else {
              echo Html::a('', ['decline', 'id' => $model->id],
               ['class' => 'btn btn-primary glyphicon glyphicon-remove-circle btn-danger',
               'title'=>Yii::t('frontend','Decline invitation'),
              ]);
            }

          ?>

          <?php
            if ($isOwner) {
                echo Html::a('', ['update', 'id' => $model->id], ['class' => 'btn btn-primary glyphicon glyphicon-pencil','title'=>'Edit']);
              }
            ?>
          </div>
        </div>
    </div> <!-- end row -->
    </div>
   </div>

        <?php if ($isOwner) {
          echo $this->render('../participant/_panel', [
              'model'=>$model,
              'participantProvider' => $participantProvider,
          ]);
        }
         ?>

        <?php
          if (!($model->meeting_type == \frontend\models\Meeting::TYPE_PHONE || $model->meeting_type == \frontend\models\Meeting::TYPE_VIDEO)) {
            echo $this->render('../meeting-place/_panel', [
              'model'=>$model,
              'placeProvider' => $placeProvider,
              'isOwner' => $isOwner,
              'viewer' => $viewer,
          ]);
          }
           ?>

        <?php echo $this->render('../meeting-time/_panel', [
            'model'=>$model,
            'timeProvider' => $timeProvider,
            'isOwner' => $isOwner,
            'viewer' => $viewer,
            'timezone'=> $timezone,
        ]) ?>

        <?php echo $this->render('../meeting-note/_panel', [
            'model'=>$model,
            'noteProvider' => $noteProvider,
        ]) ?>

</div>
<?php
if (isset(Yii::$app->params['urlPrefix'])) {
  $urlPrefix = Yii::$app->params['urlPrefix'];
  } else {
    $urlPrefix ='';
  }

$session = Yii::$app->session;
if ($session['displayHint']=='on' || $model->status == $model::STATUS_PLANNING ) {
  $notifierOkay='off';
  $session->remove('displayHint');
} else {
  $notifierOkay='on';
}

?>
<input id="notifierOkay" value="<?= $notifierOkay ?>" type="hidden">
<?php
$script = <<< JS
var notifierOkay; // meeting sent already and no page change session flash

if  ($('#notifierOkay').val() == 'on') {
  notifierOkay = true;
} else {
  notifierOkay = false;
}

function displayNotifier(mode) {
  if (notifierOkay) {
    if (mode == 'time') {
      $('#notifierTime').show();
    } else if (mode == 'place') {
       $('#notifierPlace').show();
    } else {
      alert("We\'ll automatically notify the organizer when you're done making changes.");
    }
    notifierOkay=false;
  }
}

function refreshSend() {
  $.ajax({
     url: '$urlPrefix/meeting/cansend',
     data: {id: $model->id, 'viewer_id': $viewer},
     success: function(data) {
       if (data)
         $('#actionSend').removeClass("disabled");
        else
        $('#actionSend').addClass("disabled");
       return true;
     }
  });
}

function refreshFinalize() {
  $.ajax({
     url: '$urlPrefix/meeting/canfinalize',
     data: {id: $model->id, 'viewer_id': $viewer},
     success: function(data) {
       if (data)
         $('#actionFinalize').removeClass("disabled");
        else
        $('#actionFinalize').addClass("disabled");
       return true;
     }
  });
}

JS;
$position = \yii\web\View::POS_READY;
$this->registerJs($script, $position);
?>
