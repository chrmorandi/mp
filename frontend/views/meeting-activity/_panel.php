<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
use \kartik\switchinput\SwitchInput;
?>
<div id="notifierActivity" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer activity you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingActivity">
    <div class="row"><div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view">
      <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseActivity" aria-expanded="true" aria-controls="collapseActivity"><?= Yii::t('frontend','Activity') ?></a>
    </h4>
    <span class="hint-text">
      <?php if ($activityProvider->count<=1) { ?>
        <?= Yii::t('frontend','add one or more activities for participants to choose from') ?>
    <?php } elseif ($activityProvider->count>1) { ?>
      <?= Yii::t('frontend','which activities do you prefer?'); ?>
    <?php
      }
    ?>
    <?php if ($activityProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_activity'])) { ?>
      <?= Yii::t('frontend','you can also select the activity below') ?>
    <?php }?>
  </span></div><div class="col-lg-2 col-md-2 col-xs-2"><div style="float:right;">
    <?php
      if ($model->isOrganizer() || $model->meetingSettings->participant_add_activity) { ?>
        <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary glyphicon glyphicon-plus','title'=>'Add ideas for activities','id'=>'buttonActivity','onclick'=>'showActivity();']); ?>
        <?php
      }
    ?>
      </div>
    </div>
  </div> <!-- end row -->
</div> <!-- end heading -->
  <div id="collapseActivity" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingActivity">
    <div class="panel-activity">
      <div class="activity-form hidden">
        <div id="activityMessage" class="alert-info alert fade in hidden">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <span id="activityMsg1"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.')?></span>
          <span id="activityMsg2"><?= Yii::t('frontend','Please choose an activity.')?></span>
        </div>
        <div id="addActivity" class="hidden">
          <!-- hidden add activity form -->
          <?= $this->render('_form', [
              'model' => $meetingActivity,
          ]) ?>
        </div>
      </div>
    <table class="table" id="meeting-activity-list" class="hidden">
  <?php
   if ($activityProvider->count>0):
  ?>
  <!-- Table -->
    <?= ListView::widget([
           'dataProvider' => $activityProvider,
           'itemOptions' => ['class' => 'item'],
           'layout' => '{items}',
           'itemView' => '_list',
           'viewParams' => ['activityCount'=>$activityProvider->count,
           'isOwner'=>$isOwner,'participant_choose_activity'=>$model->meetingSettings['participant_choose_activity'],
           'activityStatus'=>$activityStatus,
         ],
       ]) ?>
  <?php endif; ?>
  </table>
  </div>
  <div id="activity-choices">
  <?php if ($activityProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_activity'])) { ?>
    <?= $this->render('../meeting-activity/_choices', [
          'model'=>$model,
      ]);
       ?>
  <?php }?>
  </div>
</div>
</div>
