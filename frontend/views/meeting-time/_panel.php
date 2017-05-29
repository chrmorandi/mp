<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
use \kartik\switchinput\SwitchInput;
?>
<div id="notifierTime" class="alert-info alert fade in">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify others when you're done making changes."); ?>
</div>
<div class="panel panel-meeting" id="jumpTime">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingWhen">
    <div class="row"><div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view">
      <?= Yii::t('frontend','When') ?>
    </h4>
    <!-- <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhen" aria-expanded="true" aria-controls="collapseWhen"> -->
    <span class="hint-text">
      <?php if ($timeProvider->count==0) { ?>
        <?= Yii::t('frontend','add dates and times for participants to choose from') ?>
    <?php //} elseif ($timeProvider->count>1) { ?>
      <!--  Yii::t('frontend','are listed times okay?');  -->
    <?php
      }
    ?>
    <?php if ($timeProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_date_time'])) { ?>
      <!-- Yii::t('frontend','you can also select the time below') -->
    <?php }?>
  </span></div><div class="col-lg-2 col-md-2 col-xs-2"><div style="float:right;">
    <?php
      if ($model->isOrganizer() || $model->meetingSettings->participant_add_date_time) { ?>
        <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-time button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary','title'=>'Add possible times','id'=>'buttonTime']); ?>
        <?php
      }
    ?>
      </div>
    </div>
  </div> <!-- end row -->
</div> <!-- end heading -->
  <div id="collapseWhen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhen">
    <div class="panel-when">
      <div class="when-form hidden">
        <div id="timeMessage" class="alert-info alert fade in hidden">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <span id="timeMsg1"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.')?></span>
          <span id="timeMsg2"><?= Yii::t('frontend','Please pick a date and time.')?></span>
        </div>
        <div id="addTime" class="hidden">
          <!-- hidden add time form -->
          <?= $this->render('_form', [
              'model' => $meetingTime,
              'timezone' => $timezone,
          ]) ?>
        </div>
      </div>
      <div id="when-choices">
      <?php if ($timeProvider->count>=1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_date_time'])) { ?>
        <?= $this->render('../meeting-time/_choices', [
              'model'=>$model,
              'timezone'=>$timezone,
          ]);
           ?>
      <?php }?>
    </div> <!-- end when-choices-->
    <div id="possible-times" class="panel-body <?= ($timeProvider->count==0 || $model->isOrganizer() ?'hidden':'') ?>">
          <div class="row">
            <div class="col-xs-12" >
              <h5 id="available-times-msg" class="<?= ($timeProvider->count>1?'':'hidden') ?>"><?= Yii::t('frontend','Show Others When You\'re Available') ?></h5>
            </div>
          </div>
    <table class="table" id="meeting-time-list" class="hidden">
      <?php
       if ($timeProvider->count>0):
      ?>
      <!-- Table -->
        <?= ListView::widget([
               'dataProvider' => $timeProvider,
               'itemOptions' => ['class' => 'item'],
               'layout' => '{items}',
               'itemView' => '_list',
               'viewParams' => ['timezone'=>$timezone,'timeCount'=>$timeProvider->count,'isOwner'=>$isOwner,'participant_choose_date_time'=>$model->meetingSettings['participant_choose_date_time'],'whenStatus'=>$whenStatus],
           ]) ?>
      <?php endif; ?>
    </table>
    <?php
      if ($timeProvider->getTotalCount()>0) {
        $duration=$timeProvider->getModels()[0]['duration'];
      } else {
        $duration=60;
      }
      echo Html::hiddenInput('meeting_duration',$duration,['id'=>'meeting_duration']);
      echo Html::hiddenInput('number_times',$timeProvider->count,['id'=>'number_times']);
    ?>
  </div> <!-- end possible-times -->
</div> <!-- end panel-when -->
</div>
</div>
