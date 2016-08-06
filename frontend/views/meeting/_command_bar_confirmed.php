
<?php
use yii\helpers\Html;
// to do - note this will offer other command button options in future
  if ( $model->status < $model::STATUS_COMPLETED) {
?>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-6"></div>
      <div class="col-lg-6" >
        <div style="float:right;">

          <!--  to do - check meeting settings if participant can send/finalize -->
          <?php
          /*echo Html::a(Yii::t('frontend', 'Reschedule'), ['reschedule', 'id' => $model->id], ['id'=>'actionReschedule','class' => 'btn btn-default',
          'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);*/
          ?>
          <span class="button-pad">
            <?php
            if ( $model->status < $model::STATUS_COMPLETED) {
              echo Html::a('<i class="glyphicon glyphicon-calendar"></i>&nbsp;'.Yii::t('frontend', 'Download'), ['download', 'id' => $model->id,'actor_id'=>Yii::$app->user->getId()], ['class' => 'btn btn-default']);
            }
            ?>
          </span>
          <span class="button-pad">
            <?php
            // to do - hide both of these next buttons for meeting past
            if ( $model->status < $model::STATUS_COMPLETED) {
              echo Html::a('<i class="glyphicon glyphicon-hourglass"></i>&nbsp;'.Yii::t('frontend', 'Running Late'), ['late', 'id' => $model->id], ['class' => 'btn btn-default',
            'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);
            }
            ?>
          </span>
          <span class="button-pad">
            <?php
            if ( $model->status < $model::STATUS_COMPLETED) {
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
  </div> <!-- end head -->
</div>
<?php
  }
  ?>
