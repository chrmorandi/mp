<?php
use yii\helpers\Html;
use \common\comtonents\MiscHelpers;
use \frontend\models\Meeting;
use \frontend\models\MeetingTime;
?>
<div class="panel-body selection-panel">
  <div class="row">
    <div class="col-xs-12" >
      <h5 ><?= Yii::t('frontend','Decide the Date and Time for your {mtg_singular}',['mtg_singular'=>Yii::t('frontend',Yii::$app->params['site']['mtg_singular'])]) ?></h5>
      <p class="hint-text">
            <?= Yii::t('frontend','As an organizer, you\'re allowed to make the final choice') ?>
      </p>
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
          $btn_color = 'btn-primary';
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
    </div>
  </div>
</div>
