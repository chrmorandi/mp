<?php
use yii\helpers\Html;
use \common\comtonents\MiscHelpers;
use \frontend\models\Meeting;
use \frontend\models\MeetingTime;
?>
<div class="panel-body selection-panel">
  <div class="row">
    <div class="col-xs-12" >
      <?php
        if (count($model->meetingTimes)>1) {
      ?>
      <h5 ><?= Yii::t('frontend','Decide the date and time') ?></h5>
      <p class="hint-text">
            <?= Yii::t('frontend','As an organizer, you\'re allowed to make the final choice') ?>
      </p>
      <?php
      } else { // just one mtg time
      ?>
      <h5 ><?= Yii::t('frontend','Your current choice') ?></h5>
      <p class="hint-text">
          <?= Yii::t('frontend','As an organizer, you\'re allowed to suggest additional times') ?>
      </p>
      <?php
        }
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" >
      <div id="notifierChooseTime" class="alert-info alert fade in" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::t('frontend',"We'll automatically notify the others when you're done making changes."); ?>
      </div>
      <?php
      foreach ($model->meetingTimes as $mt) {
        if ($mt->status == MeetingTime::STATUS_REMOVED) continue;
        $btn_color = 'btn-default';
        if ($mt->status == MeetingTime::STATUS_SELECTED) {
          $btn_color = 'btn-success';
        }
      ?>
      <div class="btn-group btn-meetingtime">
        <button id="btn_mt_<?= $mt->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
          <?= Meeting::friendlyDateFromTimestamp($mt->start,$timezone,true,false,true); ?>
        </button>
      </div>
      <?php
      }
       ?>
       <?php
        if ($model->status > $model::STATUS_PLANNING && $model->isOrganizer()) {
        ?>
        <div class="reviewAvailability">
          <a href="javascript::return false;" onclick="showPossible('possible-times');"><?= Yii::t('frontend','review shared availability');?></a>
        </div>
        <?php
        }
        ?>
    </div>
  </div>
</div>
