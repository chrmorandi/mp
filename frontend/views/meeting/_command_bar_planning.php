<?php
use yii\helpers\Html;
?>
<div class="command-bar">
  <!-- Default panel contents -->
    <div class="row">
      <div class="col-xs-4">
        <div class="dropup" >
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?= Yii::t('frontend','Options');?>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
          <?php
          if ($isOwner) {
            ?>
            <li>
            <?php
            echo Html::a(Yii::t('frontend', 'Cancel'), ['cancel', 'id' => $model->id],
             [
             'title'=>Yii::t('frontend','Cancel'),
             'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
            ]);
            ?>
            </li>
          <?php
          }
          ?>
        <li role="separator" class="divider"></li>
        <?php
          if ($model->status >= $model::STATUS_SENT && $model->viewer == $model::VIEWER_ORGANIZER) {
            ?>
            <li><?= Html::a(Yii::t('frontend', 'Resend invitations'), ['/meeting/resend','id' => $model->id],
             [
             'title'=>Yii::t('frontend','Email invitations again to participants'),
            ]); ?></li>
          <?php
          }
          ?>
        <li><?= Html::a(Yii::t('frontend', 'History'), ['/meeting-log/view', 'id' => $model->id],
         [
         'title'=>Yii::t('frontend','View the historical log of meeting adjustments'),
        ]); ?></li>
        <?php
        if ($isOwner) {
          ?>
          <li>
          <?= Html::a(Yii::t('frontend', 'Preferences'), ['/meeting-setting/update', 'id' => $model->id],
           [
           'title'=>Yii::t('frontend','Update the settings for this meeting'),
          ]); ?>
          </li>
        <?php
        }
        ?>
        </ul>
        </div>
      </div>
      <div class="col-xs-8" >
        <div style="float:right;">
          <!--  to do - check meeting settings if participant can send/finalize -->
          <span class="button-pad">
    <?php
    if ($isOwner && $model->status < $model::STATUS_SENT)
     {
    echo Html::a('<i class="glyphicon glyphicon-send"></i>&nbsp;'.Yii::t('frontend', 'Send'), ['send', 'id' => $model->id], ['id'=>'actionSend','class' => 'btn btn-primary '.(!$model->isReadyToSend?'disabled':'')]);
    }
  ?>
    </span>
    <span class="button-pad">
        <?php
        if (($isOwner || $model->meetingSettings->participant_finalize) && $model->status<$model::STATUS_CONFIRMED) {
          echo Html::a('<i class="glyphicon glyphicon-time"></i>&nbsp;'.Yii::t('frontend', 'Finalize'), ['finalize', 'id' => $model->id], ['id'=>'actionFinalize','class' => 'btn btn-success '.(!$model->isReadyToFinalize?'disabled':'')]);
        }
         ?>
         </span>
         <span class="button-pad">
        <?php
          if (!$isOwner) {
            echo Html::a(Yii::t('frontend', 'Decline'), ['decline', 'id' => $model->id],
             ['class' => 'btn btn-primary  btn-danger',
             'title'=>Yii::t('frontend','Decline invitation'),
            ]);
          }
        ?>
      </span>
      </div>
      </div>
    </div> <!-- end row -->
</div> <!-- end command-bar -->
