<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
?>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"  role="tab" id="headingWho">
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10">
        <h4 class="meeting-view"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWho" aria-expanded="true" aria-controls="collapseWho"><?= Yii::t('frontend','Who') ?></a></h4>
        <span class="hint-text"><?= Yii::t('frontend','add meeting participants') ?></span>
      </div>
    <div class="col-lg-2 col-md-2 col-xs-2">
      <div style="float:right;">
        <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary '.((!$model->isOrganizer() || $model->status>=$model::STATUS_CONFIRMED)?'disabled':'').' glyphicon glyphicon-user','title'=>'Add participants','onclick'=>'showParticipant();']); ?>
      </div>
    </div>
  </div>
</div>
    <div id="collapseWho" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWho">
      <div class="panel-body">
        <div id="participantMessage" class="alert-info alert fade in hidden">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <span id="participantMessageTell"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.')?></span>
        <span id="participantMessageError"><?= Yii::t('frontend','Sorry, there were errors with your email address.')?></span>
        <span id="participantMessageNoEmail"><?= Yii::t('frontend','Please provide at least one email.')?></span>
        <span id="participantMessageOnlyOne"><?= Yii::t('frontend','Please choose to add one or the other.')?></span>
        </div>
        <div id="addParticipantPanel" class="hidden">
              <?= $this->render('_form', [
                  'participant' => $participant,
                  'friends' => $friends,
              ]) ?>
        </div>
      </div>
    </div>
    <div id="participantButtons">
    <?php
    if (!empty($participantProvider) and $participantProvider->getCount()>0):
    ?>
    <?php //who
      echo $this->render('../participant/_buttons', [
          'model'=>$model,
          'participantProvider' => $participantProvider,
      ]);
     ?>
    <?php else: ?>
    <?php endif; ?>
  </div>
  </div>
  <?php if ($model->isOrganizer()) { ?>
  <div class="panel-footer short-footer">
    <span class="hint-text">
      Or, share
    <?= Html::a($model->getSharingUrl(),$model->getSharingUrl()); ?>
    to invite participants by email.
  </span>
  </div>
  <?php } ?>

</div>

</div>
