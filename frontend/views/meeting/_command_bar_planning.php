<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use frontend\models\Participant;
?>
<div class="command-bar">
  <!-- Default panel contents -->
    <div class="row">
      <div class="col-xs-3">
        <div class="dropup" >
        <button id="button-options" class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?= Yii::t('frontend','Options');?>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
          <?php
          if ($model->isOrganizer()) {
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
            <li role="separator" class="divider"></li>
          <?php
          }
          ?>
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
        if ($model->isOrganizer()) {
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
      <div class="col-xs-9" >
        <div style="float:right;">
          <!--  to do - check meeting settings if participant can send/finalize -->
          <span class="button-pad">
    <?php
    if ($model->isOrganizer() && $model->status < $model::STATUS_SENT)
     {
    echo Html::a('<i class="glyphicon glyphicon-send"></i>&nbsp;'.Yii::t('frontend', 'Send Request'),
     ['send', 'id' => $model->id], ['id'=>'actionSend',
     'title'=>Yii::t('frontend','emails the preliminary invitation to participant(s)'),
     'class' => 'btn btn-default '.(!$model->isReadyToSend?'disabled':'')]);
    }
  ?>
    </span>
    <span class="button-pad">
        <?php
        if (($model->isOrganizer() || $model->meetingSettings->participant_finalize) && $model->status<$model::STATUS_CONFIRMED) {
          echo Html::a('<i class="glyphicon glyphicon-calendar"></i>&nbsp;'.Yii::t('frontend', 'Finalize'),
            ['finalize', 'id' => $model->id], ['id'=>'actionFinalize',
            'title'=>($model->is_activity==Meeting::NOT_ACTIVITY?'finalize the schedule only after time and place are chosen':'finalize the schedule only after activity, time and place are chosen'),
            'class' => 'btn btn-primary '.(!$model->isReadyToFinalize?'disabled':'')]);
        }
         ?>
         </span>
         <span class="button-pad">
        <?php
          if (!$model->isOrganizer() && $model->getParticipantStatus(Yii::$app->user->getId())==Participant::STATUS_DEFAULT) {
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
