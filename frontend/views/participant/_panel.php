<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
?>
<div id="jumpTime"></div>
<div class="panel panel-meeting">
  <!-- Default panel contents -->
  <div class="panel-heading"  role="tab" id="headingWho">
    <div class="row">
      <div class="col-lg-9 col-md-8 col-xs-6">
        <h4 class="meeting-view"><?= Yii::t('frontend','Who') ?></h4>
        <!-- <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWho" aria-expanded="true" aria-controls="collapseWho"> -->
         <!-- <span class="hint-text">< ?= Yii::t('frontend','the people attending') ? ></span>-->
      </div>
    <div class="col-lg-3 col-md-4 col-xs-6">
      <div style="float:right;">
        <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-user button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary button-margin-top'.((!$model->isOrganizer() || $model->status>=$model::STATUS_CONFIRMED)?'disabled':''),'aria-label'=>Yii::t('frontend','Add people'),'title'=>'Add participants','onclick'=>'showWhoEmail();']); ?>
        <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary '.($friendCount==0?'hidden ':' ').((!$model->isOrganizer() || $model->status>=$model::STATUS_CONFIRMED)?'disabled':'').' glyphicon glyphicon-book','title'=>'Add favorites','onclick'=>'showWhoFavorites();']); ?>
      </div>
    </div>
  </div>
</div>
    <div id="collapseWho" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWho">
      <div class="panel-body panel-who">
        <div id="participantMessage" class="alert-info alert fade in hidden">
        <button type="button" class="close" onclick="$('#participantMessage').addClass('hidden');" aria-hidden="true">&times;</button> <!-- data-dismiss="alert" -->
        <span id="participantMessageStatus"><?= Yii::t('frontend','Please wait. Adding recipients may take a few seconds...')?></span>
        <span id="participantMessageTell"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes to the meeting.')?></span>
        <span id="participantMessageError"><?= Yii::t('frontend','Sorry, there were errors with your email address.')?></span>
        <span id="participantMessageNoEmail"><?= Yii::t('frontend','Please provide at least one email.')?></span>
        <span id="participantMessageOnlyOne"><?= Yii::t('frontend','Please choose to add one or the other.')?></span>
        </div>
        <div id="addParticipantHint" class="centered">
          <?php
            if ((!empty($participantProvider) && $participantProvider->getCount()==0) && $model->isOrganizer() && $model->status<$model::STATUS_CONFIRMED) {
              ?>
              <?= Yii::t('frontend','Click');?> <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-user button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary '.((!$model->isOrganizer() || $model->status>=$model::STATUS_CONFIRMED)?'disabled':'').' mini-button mini-button-pad','title'=>'Add participants','onclick'=>'showWhoEmail();']); ?> <?= Yii::t('frontend','to add people'); ?>
          <?php
            }
          ?>
        </div>
        <div id="addParticipantPanel" class="hidden">
              <?= $this->render('_form', [
                  'participant' => $participant,
                  'friends' => $friends,
              ]) ?>
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
    <div id="invitation-url" class="hint-text">
    <span class="glyphicon glyphicon-link"></span>&nbsp;<?= Yii::t('frontend','Or, invite participants by sharing');?> <?= Html::a($model->getSharingUrl(),$model->getSharingUrl()); ?>
    </div><br style="clear:both;">
  </div>
  <?php } ?>
</div>

</div>
