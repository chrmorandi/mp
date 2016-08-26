<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use frontend\models\MeetingSetting;
use frontend\models\Participant;
global $cnt_items;
$cnt_items=0;
?>
  <div class="command-bar">
    <div class="row">
      <div class="col-xs-4">
        <div class="<?= $dropclass ?>" >
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?= Yii::t('frontend','Options');?>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
          <?php
            if (!$isPast && ($model->isOrganizer() || $meetingSettings->participant_reopen)) {
              $cnt_items+=1;
              ?>
              <li><?= Html::a(Yii::t('frontend', 'Make changes'), ['reopen','id'=>$model->id],
               ['title'=>Yii::t('frontend','Change the time and place of the meeting by returning it to planning mode.')]); ?></li>
          <?php
            }
           ?>
           <?php
             if (!$isPast && ($model->isOrganizer() || $meetingSettings->participant_request_change)) {
               $cnt_items+=1;
               ?>
               <li><?= Html::a(Yii::t('frontend', 'Request changes'), ['/request/create','meeting_id'=>$model->id],
                ['title'=>Yii::t('frontend','Request a change to the time and place of other participant(s)')]); ?></li>
             <?php
             }
             ?>
             <?php
               if (!$isPast && $model->isOrganizer()) {
                 $cnt_items+=1;
                 ?>
                 <li><?= Html::a(Yii::t('frontend', 'Reschedule'), ['reschedule','id'=>$model->id],
                  [
                  'title'=>Yii::t('frontend','Cancel this meeting and reschedule a new one with the same people and place'),
                  'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting and schedule a new one?')
                 ]); ?></li>
               <?php
             } elseif ($isPast) {
               $cnt_items+=1;
               // to do - should we allow confirmed meeting to be repeated before its past
               ?>
               <li><?= Html::a(Yii::t('frontend', 'Repeat'), ['repeat','id'=>$model->id],
                [
                'title'=>Yii::t('frontend','Repeat this meeting at a new time'),
                ]); ?></li>
               <?php
             }
               ?>
        <?php if ($cnt_items>0) {
          ?>
          <li role="separator" class="divider"></li>
          <?php
        }
        ?>
        <?php
          if (!$isPast && $model->status >= $model::STATUS_SENT && $model->viewer == $model::VIEWER_ORGANIZER) {
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
      <div class="col-xs-8" >
        <div style="float:right;">
          <span class="button-pad">
            <?php
            if (!$isPast) {
              echo Html::a('<i class="glyphicon glyphicon-calendar"></i>&nbsp;'.Yii::t('frontend', 'Download'), ['download', 'id' => $model->id,'actor_id'=>Yii::$app->user->getId()], ['class' => 'btn btn-default']);
            }
            ?>
          </span>
          <span class="button-pad">
            <?php
            if ( $showRunningLate ) {
              echo Html::a('<i class="glyphicon glyphicon-hourglass"></i>&nbsp;'.Yii::t('frontend', 'Running Late'), ['late', 'id' => $model->id], ['class' => 'btn btn-default',
              'title' => Yii::t('frontend', 'Notify participants that you will be late')]);
            }
            ?>
          </span>
          <span class="button-pad">
            <?php
            if (!$isPast) {
              if ($model->isOrganizer()) {
                echo Html::a('<i class="glyphicon glyphicon-remove-circle"></i>&nbsp;'.Yii::t('frontend', 'Cancel'), ['cancel', 'id' => $model->id],
               ['class' => 'btn btn-primary btn-danger',
               'title'=>Yii::t('frontend','Cancel'),
               'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
               ]) ;
              }
              else {                
                if ($model->getParticipantStatus(Yii::$app->user->getId())==Participant::STATUS_DEFAULT) {
                  echo Html::a('<i class="glyphicon glyphicon-remove-circle"></i>&nbsp;'.Yii::t('frontend', 'Withdraw'), ['decline', 'id' => $model->id],
                 ['class' => 'btn btn-primary btn-danger',
                 'title'=>Yii::t('frontend','Withdraw from the meeting'),
                 'data-confirm' => Yii::t('frontend', 'Are you sure you want to decline attendance to this meeting?')
                 ]) ;
               } else {
                 // to do - offer rejoin meeting option
               }
              }
             }
            ?>
          </span>
        </div>
      </div>
    </div> <!-- end row -->
</div>
