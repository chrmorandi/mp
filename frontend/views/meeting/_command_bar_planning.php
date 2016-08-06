<?php
use yii\helpers\Html;
?>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-6"></div>
      <div class="col-lg-6" >
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
        if (($isOwner  || $model->meetingSettings->participant_finalize) && $model->status<$model::STATUS_CONFIRMED) {
          echo Html::a('<i class="glyphicon glyphicon-time"></i>&nbsp;'.Yii::t('frontend', 'Finalize'), ['finalize', 'id' => $model->id], ['id'=>'actionFinalize','class' => 'btn btn-success '.(!$model->isReadyToFinalize?'disabled':'')]);
        }
         ?>
         </span>
         <span class="button-pad">
        <?php
          if ($isOwner) {
            echo Html::a('<i class="glyphicon glyphicon-remove-circle"></i>&nbsp;'.Yii::t('frontend', 'Cancel'), ['cancel', 'id' => $model->id],
             ['class' => 'btn btn-primary btn-danger',
             'title'=>Yii::t('frontend','Cancel'),
             'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
            ]);
          } else {
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
  </div> <!-- end head -->
 </div>
