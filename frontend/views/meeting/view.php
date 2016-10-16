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
$this->params['breadcrumbs'][] = ['label' => Yii::$app->params['site']['mtg'], 'url' => ['index']];
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
             'meetingTime'=>$meetingTime,
         ]) ?>


        <?= $this->render('../meeting-place/_panel', [
              'model'=>$model,
              'placeProvider' => $placeProvider,
              'whereStatus'=> $whereStatus,
              'isOwner' => $isOwner,
              'viewer' => $viewer,
              'meetingPlace'=>$meetingPlace,
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

<?php
  echo $this->render('_command_bar_planning', [
      'model'=>$model,
      'isOwner'=>$isOwner,
  ]);
 ?>
<?php
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
<input id="viewer" value="<?= Yii::$app->user->getId(); ?>" type="hidden">
<?= BaseHtml::hiddenInput('url_prefix',MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
<?= BaseHtml::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= BaseHtml::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
</div>
