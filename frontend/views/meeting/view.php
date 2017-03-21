<?php
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
echo $this->render('_guide_alert');
?>
<div class="meeting-view">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li id="tourStart" class="<?= ($tab=='details'?'active':'') ?>"><a href="#details" role="tab" data-toggle="tab"><?= Yii::t('frontend','Details');?></a></li>
    <li id="tourDiscussion" class="<?= ($tab=='notes'?'active':'') ?>"><a href="#notes" role="tab" data-toggle="tab"><?= Yii::t('frontend','Discussion');?></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane <?= ($tab=='details'?'active':'') ?> vertical-pad" id="details">
        <?php //who
          echo $this->render('../participant/_panel', [
              'model'=>$model,
              'participantProvider' => $participantProvider,
              'participant'=>$participant,
              'friends'=>$friends,
              'friendCount'=>$friendCount,
          ]);
         ?>
         <?= $this->render('./_panel_what', [
             'model'=>$model,
             'isOwner' => $isOwner,
         ]); ?>
         <?php
           if ($model->is_activity == $model::IS_ACTIVITY) {
             echo $this->render('../meeting-activity/_panel', [
                'model'=>$model,
                'isOwner' => $isOwner,
                'viewer' => $viewer,
                'activityProvider' => $activityProvider,
                'activityStatus'=>$activityStatus,
                'meetingActivity'=>$meetingActivity,
            ]);
           }
          ?>
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

        <div id="jumpPlace"></div>
        <?= $this->render('../meeting-place/_panel', [
              'model'=>$model,
              'placeProvider' => $placeProvider,
              'whereStatus'=> $whereStatus,
              'isOwner' => $isOwner,
              'viewer' => $viewer,
              'meetingPlace'=>$meetingPlace,
              'userPlacesCount'=>$userPlacesCount,
          ]);
           ?>
    </div> <!-- end tab details -->
    <div class="tab-pane <?= ($tab=='notes'?'active':'') ?> vertical-pad" id="notes">
        <?php
          // notes
        // removed - if ( $model->status >= $model::STATUS_SENT) {}
           echo $this->render('../meeting-note/_panel', [
               'model'=>$model,
               'noteProvider' => $noteProvider,
           ]);
        ?>
      </div> <!-- end tab notes -->
    </div> <!-- end tab content -->

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
<?= Html::hiddenInput('url_prefix',MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
<?= Html::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= Html::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
<?= Html::hiddenInput('showGuide',$showGuide,['id'=>'showGuide']); ?>
</div>
