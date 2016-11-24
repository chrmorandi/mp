<?php
use yii\helpers\Html;
use \common\comtonents\MiscHelpers;
use \frontend\models\Meeting;
use \frontend\models\MeetingTime;
?>
<div class="choiceHead">
  <div class="row">
    <div class="col-lg-10 col-md-10 col-xs-10" >
      <h5 ><?= Yii::t('frontend','Select the Time') ?></h5>
      <div class="hint-text">
        <?= Yii::t('frontend','you\'re allowed to make the choice') ?>
      </div>
    </div>
  </div>
</div>
<div class="panel-body">
  <div class="row">
    <div class="col-xs-12" >
      <div id="notifierChooseTime" class="alert-info alert fade in" style="display:none;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
      </div>
  <?php
  foreach ($model->meetingTimes as $mt) {
    if ($mt->status == MeetingTime::STATUS_REMOVED) continue;
    $btn_color = 'btn-default';
    if ($mt->status == MeetingTime::STATUS_SELECTED) {
      $btn_color = 'btn-primary';
    }
  ?>
  <div class="btn-group btn-meetingtime">
    <button id="btn_mt_<?= $mt->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
      <?= Meeting::friendlyDateFromTimestamp($mt->start,$timezone); ?>
    </button>
  </div>
<?php
  }
   ?>
    </div>
  </div>
</div>
