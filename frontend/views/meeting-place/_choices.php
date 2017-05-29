<?php
use yii\helpers\Html;
use \common\components\MiscHelpers;
use \frontend\models\MeetingPlace;
?>
<div class="panel-body selection-panel">
  <div class="row">
    <div class="col-xs-12" >
      <?php
        if (count($model->meetingPlaces)>1) {
      ?>
      <h5 ><?= Yii::t('frontend','Decide the place') ?></h5>
      <p class="hint-text">
          <?= Yii::t('frontend','As an organizer, you\'re allowed to make the final choice') ?>
      </p>
      <?php
        } else { // just one place
      ?>
      <h5 ><?= Yii::t('frontend','Your current choice') ?></h5>
      <p class="hint-text">
          <?= Yii::t('frontend','As an organizer, you\'re allowed to suggest additional places') ?>
      </p>
      <?php
        }
      ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12" >
      <div id="notifierChoosePlace" class="alert-info alert fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo Yii::t('frontend',"We'll automatically notify the others when you're done making changes."); ?>
      </div>
      <?php
      foreach ($model->meetingPlaces as $mp) {
        if ($mp->status == MeetingPlace::STATUS_REMOVED) continue;
        $btn_color = 'btn-default';
        if ($mp->status == MeetingPlace::STATUS_SELECTED) {
          $btn_color = 'btn-success';
        }
      ?>
      <div class="btn-group btn-meetingplace">
        <button id="btn_mp_<?= $mp->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
          <?= $mp->place->name ?>
        </button>
      </div>
      <?php
        }
         ?>
         <?php
          if ($model->status > $model::STATUS_PLANNING && $model->isOrganizer()) {
          ?>
      <div class="reviewAvailability">
        <a href="javascript::return false;" onclick="showPossible('possible-places');"><?= Yii::t('frontend','review shared preferences');?></a>
      </div>
      <?php
    }
    ?>
    </div>
  </div> <!-- end row -->
</div> <!-- end panel -->
