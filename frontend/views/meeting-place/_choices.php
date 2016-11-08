<?php
use yii\helpers\Html;
use \common\components\MiscHelpers;
?>
<div class="choiceHead" style="background-color:#f5f5f5;">
  <div class="row">
    <div class="col-lg-10 col-md-10 col-xs-10" >
      <h5 ><?= Yii::t('frontend','Make the Choice') ?></h5>
      <div class="hint-text">
        <?= Yii::t('frontend','you\'re allowed to choose the final place') ?>
      </div>
    </div>
  </div>
</div>
<div class="panel-body">
  <div class="row">
    <div class="col-xs-12" >
  <?php
  foreach ($model->meetingPlaces as $mp) {
    $btn_color = 'btn-default';
    if ($mp->id==356) {
      $btn_color = 'btn-success';
    }
    // jscript on click
    // loop thru with id
    // remove class color
    // add class color
    // input field with selected place
    // also relates to finalizing (refreshsend and refreshfinalize)
  ?>
  <div class="btn-group btn-participant">
    <button id="btn_<?= $mp->id ?>" type="button" class="btn btn-sm <?= $btn_color ?>" >
      <?= $mp->place->name ?>
    </button>
  </div>
<?php
  }
   ?>
    </div>
  </div>
</div>
