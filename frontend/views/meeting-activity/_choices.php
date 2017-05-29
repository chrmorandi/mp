<?php
use yii\helpers\Html;
use \common\components\MiscHelpers;
use \frontend\models\Meeting;
use \frontend\models\MeetingActivity;
?>
<div class="panel-body selection-panel">
  <div class="row">
    <div class="col-xs-12" >
      <?php
        if (count($model->meetingActivities)>1) {
      ?>
      <h5 ><?= Yii::t('frontend','Decide the Activity for your {mtg_singular}',['mtg_singular'=>Yii::$app->params['site']['mtg_singular']]) ?></h5>
      <p class="hint-text">
            <?= Yii::t('frontend','As an organizer, you\'re allowed to make the final choice') ?>
      </p><?php
        } else { // just one activity
      ?>
      <h5 ><?= Yii::t('frontend','Your current choice') ?></h5>
      <p class="hint-text">
          <?= Yii::t('frontend','As an organizer, you\'re allowed to suggest additional activities') ?>
      </p>
      <?php
        }
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" >
      <div id="notifierChooseActivity" class="alert-info alert fade in" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::t('frontend',"We'll automatically notify the others when you're done making changes."); ?>
      </div>
  <?php
  foreach ($model->meetingActivities as $ma) {
    if ($ma->status == MeetingActivity::STATUS_REMOVED) continue;
      $btn_color = 'btn-default';
    if ($ma->status == MeetingActivity::STATUS_SELECTED) {
      $btn_color = 'btn-success';
    }
  ?>
  <div class="btn-group btn-meetingactivity">
    <button id="btn_ma_<?= $ma->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
      <?= $ma->activity ?>
    </button>
  </div>
<?php
  }
   ?><?php
    if ($model->status > $model::STATUS_PLANNING && $model->isOrganizer()) {
    ?>
<div class="reviewAvailability">
  <a href="javascript::return false;" onclick="showPossible('possible-activities');"><?= Yii::t('frontend','review shared preferences');?></a>
</div>
<?php
}
?>
    </div>
  </div>
</div> <!-- end panel -->
