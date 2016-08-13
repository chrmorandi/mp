
<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use frontend\models\MeetingSetting;
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
            if (!$isPast && ($model->viewer == Meeting::VIEWER_ORGANIZER || $meetingSettings->participant_reopen)) {
              ?>
              <li><?= Html::a(Yii::t('frontend', 'Make changes'), ['reopen','id'=>$model->id],
               ['title'=>Yii::t('frontend','tbd')]); ?></li>
          <?php
            }
           ?>
           <?php
             if (!$isPast && ($model->viewer == Meeting::VIEWER_ORGANIZER || $meetingSettings->participant_request_change)) {
               ?>
               <li><?= Html::a(Yii::t('frontend', 'Request changes'), ['/request/create','meeting_id'=>$model->id],
                ['title'=>Yii::t('frontend','tbd')]); ?></li>
             <?php
             }
             ?>
             <?php
               if (!$isPast && $model->viewer == Meeting::VIEWER_ORGANIZER) {
                 ?>
                 <li><?= Html::a(Yii::t('frontend', 'Reschedule'), ['reschedule','id'=>$model->id],
                  [
                  'title'=>Yii::t('frontend','Cancel this meeting and reschedule a new one with the same people and place'),
                  'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting and schedule a new one?')
                 ]); ?></li>
               <?php
             } elseif ($isPast) {
               ?>
               <li><?= Html::a(Yii::t('frontend', 'Repeat'), ['repeat','id'=>$model->id],
                [
                'title'=>Yii::t('frontend','Repeat this meeting at a new time'),
                ]); ?></li>
               <?php
             }
               ?>
        <li role="separator" class="divider"></li>
        <?php
          if (!$isPast && $model->status >= $model::STATUS_SENT) {
            ?>
            <li><?= Html::a(Yii::t('frontend', 'Resend invitations'), ['/site/unavailable'],
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
            if ( !$isPast) {
              echo Html::a('<i class="glyphicon glyphicon-remove-circle"></i>&nbsp;'.Yii::t('frontend', 'Cancel'), ['cancel', 'id' => $model->id],
             ['class' => 'btn btn-primary btn-danger',
             'title'=>Yii::t('frontend','Cancel'),
             'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
             ]) ;
              }
         ?>
          </span>
        </div>
      </div>
    </div> <!-- end row -->
</div>
