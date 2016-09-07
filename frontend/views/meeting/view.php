<?php
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use common\components\MiscHelpers;
use frontend\assets\MeetingAsset;
MeetingAsset::register($this);
/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */
$this->title = $model->getMeetingHeader('view');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_timezone_alerts');
?>
<div class="meeting-view">
        <?php //who
          echo $this->render('../participant/_panel', [
              'model'=>$model,
              'participantProvider' => $participantProvider,
              'participant'=>$participant,
              'friends'=>$friends,
          ]);
         ?>

         <?php  // what
         echo $this->render('./_panel_what', [
             'model'=>$model,
             'isOwner' => $isOwner,
         ]) ?>

         <?php // when
          echo $this->render('../meeting-time/_panel', [
             'model'=>$model,
             'timeProvider' => $timeProvider,
             'whenStatus'=> $whenStatus,
             'isOwner' => $isOwner,
             'viewer' => $viewer,
             'timezone'=> $timezone,
         ]) ?>

        <?php
          // where
            echo $this->render('../meeting-place/_panel', [
              'model'=>$model,
              'placeProvider' => $placeProvider,
              'whereStatus'=> $whereStatus,
              'isOwner' => $isOwner,
              'viewer' => $viewer,
          ]);
           ?>

        <?php
          // notes
        if ( $model->status >= $model::STATUS_SENT)
         {
           echo $this->render('../meeting-note/_panel', [
               'model'=>$model,
               'noteProvider' => $noteProvider,
           ]);
        }
        ?>

</div>

<?php
  echo $this->render('_command_bar_planning', [
      'model'=>$model,
      'isOwner'=>$isOwner,
  ]);
 ?>
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
<input id="meeting_id" value="<?= $model->id; ?>" type="hidden">
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
<?= BaseHtml::hiddenInput('url_prefix',MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
<?= BaseHtml::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= BaseHtml::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
