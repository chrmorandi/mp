<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\components\MiscHelpers;
use frontend\models\Participant;
if (count($model->participants)>0) {
  foreach ($model->participants as $p) {
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
            <li id="mo_<?= $p->id ?>" class="<?= ($p->isOrganizer())?'hidden':''?>"><?= Html::a(Yii::t('frontend','Make Organizer'),'javascript:void(0);',['onclick' => "toggleOrganizer($p->id,true);return false;"]); ?></li>
            <li id="ro_<?= $p->id ?>" class="<?= (!$p->isOrganizer())?'hidden':''?>"><?= Html::a(Yii::t('frontend','Revoke Organizer Role'),'javascript:void(0);',['onclick' => "toggleOrganizer($p->id,false);return false;"]); ?></li>
          <li id="rp_<?= $p->id ?>" class="<?= ($p->status == Participant::STATUS_REMOVED || $p->status == Participant::STATUS_DECLINED_REMOVED)?'hidden':''?>"><?= Html::a(Yii::t('frontend','Remove Participant'),'javascript:void(0);',['onclick' => "toggleParticipant($p->id,false,$p->status);return false;"]); ?></li>
          <li id="rstp_<?= $p->id ?>" class="<?= ($p->status != Participant::STATUS_REMOVED && $p->status != Participant::STATUS_DECLINED_REMOVED)?'hidden':''?>"><?= Html::a(Yii::t('frontend','Restore Participant'),'javascript:void(0);',['onclick' => "toggleParticipant($p->id,true,$p->status);return false;"]); ?></li>
          <?php
          }
          ?>
    </ul>
  </div>
<?php
  }
}
/* old row by row was within a table
= ListView::widget([
   'dataProvider' => $participantProvider,
   'itemOptions' => ['class' => 'item'],
   'layout' => '{items}',
   'itemView' => '_list',
]) */
?>
