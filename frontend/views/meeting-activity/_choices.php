<?php
use yii\helpers\Html;
use \common\components\MiscHelpers;
use \frontend\models\Meeting;
use \frontend\models\MeetingActivity;
?>
<div class="choiceHead">
  <div class="row">
    <div class="col-lg-10 col-md-10 col-xs-10" >
      <h5 ><?= Yii::t('frontend','Finalize the Activity') ?></h5>
      <div class="hint-text">
        <?= Yii::t('frontend','you\'re allowed to make the choice') ?>
      </div>
    </div>
  </div>
</div>
<div class="panel-body">
  <div class="row">
    <div class="col-xs-12" >
      <div id="notifierChooseActivity" class="alert-info alert fade in" style="display:none;">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
      </div>
  <?php
  foreach ($model->meetingActivities as $ma) {
    if ($ma->status == MeetingActivity::STATUS_REMOVED) continue;
      $btn_color = 'btn-default';
    if ($ma->status == MeetingActivity::STATUS_SELECTED) {
      $btn_color = 'btn-primary';
    }
  ?>
  <div class="btn-group btn-meetingactivity">
    <button id="btn_ma_<?= $ma->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
      <?= $ma->activity ?>
    </button>
  </div>
<?php
  }
   ?>
    </div>
  </div>
</div>
