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
?>
<div class="meeting-view">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li class="<?= ($tab=='details'?'active':'') ?>"><a href="#details" role="tab" data-toggle="tab"><?= Yii::t('frontend','Details');?></a></li>
    <li class="<?= ($tab=='notes'?'active':'') ?>"><a href="#notes" role="tab" data-toggle="tab"><?= Yii::t('frontend','Discussion');?></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane <?= ($tab=='details'?'active':'') ?> vertical-pad" id="details">
  <?= $this->render('./_panel_what', [
      'model'=>$model,
      'isOwner' => $isOwner,
  ]); ?>

        <?php //who
          echo $this->render('../participant/_panel', [
              'model'=>$model,
              'participantProvider' => $participantProvider,
              'participant'=>$participant,
              'friends'=>$friends,
          ]);
         ?>
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
</div>
<script src="//cdn.ably.io/lib/ably.min.js"></script>
<script type="text/javascript">
  var realtime = new Ably.Realtime({key: 'KqTFOw.Av_YnA:dT3V7kmT6jO-T6Ju', clientId: 'apple'});
  var channel = realtime.channels.get('chatroom');
channel.attach(function(err) {
  if(err) { return console.error("Error attaching to the channel"); }
  console.log('We are now attached to the channel');

  channel.presence.update('Comments!!', function(err) {
    if(err) { return console.error("Error updating presence data"); }
    console.log('We have successfully updated our data');
  })
});

channel.presence.get(function(err, members) {
  if(err) { return console.error("Error fetching presence data"); }
  console.log('There are ' + members.length + ' clients present on this channel');
  var first = members[0];
  console.log('The first member is ' + first.clientId);
  console.log('and their data is ' + first.data);

});
channel.presence.subscribe(function(presenceMsg) {
  console.log('Received a ' + presenceMsg.action + ' from ' + presenceMsg.clientId);
  channel.presence.get(function(err, members) {
    console.log('There are now ' + members.length + ' clients present on this channel');
  });
});
</script>
