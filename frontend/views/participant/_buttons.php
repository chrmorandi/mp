<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\MiscHelpers;
use frontend\models\Participant;
// show organizer
if (!$model->isOwner(Yii::$app->user->getId())) {
?>
<div class="btn-group btn-participant">
  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <span class="glyphicon glyphicon-star red-star" aria-hidden="true"></span>
    <?= MiscHelpers::getDisplayName($model->owner_id) ?>
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
      <li><?= Html::a(Yii::t('frontend','Send a message'),Url::to('mailto:'.$model->owner->email),['target' => '_blank'])?></li>
  </ul>
</div>
<?php
}
?>
<?php
if (count($model->participants)>0) {
  foreach ($model->participants as $p) {
    // note participant is the user model, so id okay here
    if ($p->participant->id==Yii::$app->user->getId()) {
      continue;
    }
    $btn_color = 'btn-default';
    if ($p->status == Participant::STATUS_DECLINED) {
      $btn_color = 'btn-warning';
    } else if ($p->status == Participant::STATUS_REMOVED || $p->status == Participant::STATUS_DECLINED_REMOVED) {
      $btn_color = 'btn-danger';
    }
  ?>
  <div class="btn-group btn-participant">
    <button id="btn_<?= $p->id ?>" type="button" class="btn <?= $btn_color ?> btn-sm dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span id="star_<?= $p->id ?>" class="glyphicon glyphicon-star red-star <?= (!$p->isOrganizer())?'hidden':''?>" aria-hidden="true"></span>
      <?= MiscHelpers::getDisplayName($p->participant->id) ?>
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><?= Html::a(Yii::t('frontend','Send a message'),Url::to('mailto:'.$p->participant->email))?></li>
        <?php if ($model->isOrganizer()) {
          ?>
          <li role="separator" class="divider"></li>
            <li id="mo_<?= $p->id ?>" class="<?= ($p->isOrganizer())?'hidden':''?>"><?= Html::a(Yii::t('frontend','Make organizer'),'javascript:void(0);',['onclick' => "toggleOrganizer($p->id,true);return false;"]); ?></li>
            <li id="ro_<?= $p->id ?>" class="<?= (!$p->isOrganizer())?'hidden':''?>"><?= Html::a(Yii::t('frontend','Revoke organizer role'),'javascript:void(0);',['onclick' => "toggleOrganizer($p->id,false);return false;"]); ?></li>
          <li id="rp_<?= $p->id ?>" class="<?= ($p->status == Participant::STATUS_REMOVED || $p->status == Participant::STATUS_DECLINED_REMOVED)?'hidden':''?>"><?= Html::a(Yii::t('frontend','Remove participant'),'javascript:void(0);',['onclick' => "toggleParticipant($p->id,false,$p->status);return false;"]); ?></li>
          <li id="rstp_<?= $p->id ?>" class="<?= ($p->status != Participant::STATUS_REMOVED && $p->status != Participant::STATUS_DECLINED_REMOVED)?'hidden':''?>"><?= Html::a(Yii::t('frontend','Restore participant'),'javascript:void(0);',['onclick' => "toggleParticipant($p->id,true,$p->status);return false;"]); ?></li>
          <?php
          }
          ?>
    </ul>
  </div>
<?php
  }
}
?>
