
<?php
use yii\helpers\Html;
// to do - note this will offer other command button options in future
  if ( $model->status < $model::STATUS_COMPLETED) {
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
          <li><?= Html::a(Yii::t('frontend', 'Request Changes'), ['/site/unavailable'],
           [
           'title'=>Yii::t('frontend','tbd'),
          ]); ?></li>
          <li><?= Html::a(Yii::t('frontend', 'Reschedule'), ['/site/unavailable'],
           [
           'title'=>Yii::t('frontend','tbd'),
          ]); ?></li>
        <li role="separator" class="divider"></li>
        <li><?= Html::a(Yii::t('frontend', 'Resend invitations'), ['/site/unavailable'],
         [
         'title'=>Yii::t('frontend','Email invitations again to participants'),
        ]); ?></li>
        <li><?= Html::a(Yii::t('frontend', 'History'), ['/meeting-log/view', 'id' => $model->id],
         [
         'title'=>Yii::t('frontend','View the historical log of meeting adjustments'),
        ]); ?></li>
        <li><?= Html::a(Yii::t('frontend', 'Preferences'), ['/meeting-setting/update', 'id' => $model->id],
         [
         'title'=>Yii::t('frontend','Update the settings for this meeting'),
        ]); ?></li>
        </ul>
        </div>
      </div>
      <div class="col-xs-8" >
        <div style="float:right;">

          <!--  to do - check meeting settings if participant can send/finalize -->
          <?php
          /*echo Html::a(Yii::t('frontend', 'Reschedule'), ['reschedule', 'id' => $model->id], ['id'=>'actionReschedule','class' => 'btn btn-default',
          'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);*/
          ?>
          <span class="button-pad">
            <?php
            if (!$isPast) {
              echo Html::a('<i class="glyphicon glyphicon-calendar"></i>&nbsp;'.Yii::t('frontend', 'Download'), ['download', 'id' => $model->id,'actor_id'=>Yii::$app->user->getId()], ['class' => 'btn btn-default']);
            }
            ?>
          </span>
          <span class="button-pad">
            <?php
            // to do - hide both of these next buttons for meeting past
            if ( $showRunningLate ) {
              echo Html::a('<i class="glyphicon glyphicon-hourglass"></i>&nbsp;'.Yii::t('frontend', 'Running Late'), ['late', 'id' => $model->id], ['class' => 'btn btn-default',
            'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);
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
<?php
  }
  ?>
