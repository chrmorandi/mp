<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use \kartik\switchinput\SwitchInput;
use \common\components\MiscHelpers;
?>
<div id="notifierPlace" class="alert-info alert fade in" style="display:none;">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingWhere">
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10" >
        <h4 class="meeting-place"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhere" aria-expanded="true" aria-controls="collapseWhere"><?= Yii::t('frontend','Where') ?></a></h4><p>
        <div class="hint-text heading-pad">
        <?php if ($placeProvider->count<=1) { ?>
          <?= Yii::t('frontend','add possible meeting places or switch to \'virtual\'') ?>
      <?php } elseif ($placeProvider->count>1) { ?>
          <?= Yii::t('frontend','are these places acceptable?') ?>
          <?php if ($placeProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_place'])) { ?>
            <?= Yii::t('frontend','you can also select the place below') ?>
          <?php }?>
        <?php
          }
        ?>
      </div>
    </div>

<?php
  if (!$model->isOrganizer()) {
    // To Do: Check Meeting Settings whether participant can add places
  }
?>
      <div class="col-lg-2 col-md-2 col-xs-2" >
        <div style="float:right;">
        <?php
          if ($model->isOrganizer() || $model->meetingSettings->participant_add_place) {
          ?>
          <table><tr style="vertical-align:top;"><td class="virtualThing" style="padding-left:10px;">
            <?php
            echo SwitchInput::widget([
              'type' => SwitchInput::CHECKBOX,
              'name' => 'meeting-switch-virtual',
                'value' => $model->switchVirtual,
                'pluginOptions' => [
                  'handleWidth'=>75,
                  'labelWidth'=>0,
                  'size'=>'small','onText' => '<i class="glyphicon glyphicon-user"></i>&nbsp;in person','offText'=>'<i class="glyphicon glyphicon-earphone"></i>&nbsp;virtual'], // 'onColor' => 'success','offColor' => 'danger'
                'labelOptions' => ['style' => 'font-size: 8px;'],
            ]);
            ?>
            </td><td style="padding-left:10px;">
            <?php
              if ($model->switchVirtual == $model::SWITCH_INPERSON) { ?>
                  <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary glyphicon glyphicon-plus','id'=>'meeting-add-place','title'=>'Add possible places','onclick'=>'showPlace();']); ?>
              <?php } else { ?>
                <?= Html::a('', 'javascript:void(0);', ['id'=>'meeting-add-place','class' => 'btn btn-primary glyphicon glyphicon-plus','disabled'=>true,'onclick'=>'return false;']); ?>
              <?php } ?>
            </td></tr></table>
          <?php
          }
        ?>
        </div>
    </div>
  </div> <!-- end row -->
</div> <!-- end panel heading -->
  <div id="collapseWhere" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhere">
    <div class="panel-where">
      <div class="where-form hidden">
        <div id="placeMessage" class="alert-info alert fade in hidden">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <span id="placeMsg1"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.')?></span>
          <span id="placeMsg2"><?= Yii::t('frontend','Please pick at least one place.')?></span>
          <span id="placeMsg3"><?= Yii::t('frontend','Sorry, there were errors.')?></span>
        </div>
        <div id="addPlace" class="hidden">
          <!-- hidden add time form -->
          <?= $this->render('_form', [
              'model' => $meetingPlace,
          ]) ?>
        </div>
      </div>
        <?php
      $dclass = ($model->switchVirtual==$model::SWITCH_VIRTUAL?'hidden':'');
     ?>
    <div id ="meeting-place-list" class="<?= $dclass; ?>">
    <table class="table" id="placeTable" class="hidden">
      <?php
       if ($placeProvider->count>0):
      ?>
      <?= ListView::widget([
             'dataProvider' => $placeProvider,
             'itemOptions' => ['class' => 'item'],
             'layout' => '{items}',
             'itemView' => '_list',
             'viewParams' => ['placeCount'=>$placeProvider->count,'isOwner'=>$isOwner,'participant_choose_place'=>$model->meetingSettings['participant_choose_place'],'whereStatus'=>$whereStatus],
         ]) ?>
       <?php endif; ?>
    </table>
    </div> <!-- end meeting-place-list -->
  </div> <!-- end class panel-where -->
  <div id="where-choices">
  <?php if ($placeProvider->count>1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_place'])) { ?>
    <?= $this->render('../meeting-place/_choices', [
          'model'=>$model,
      ]);
       ?>
  <?php }?>
  </div>
</div> <!-- end collapse panel where -->
</div> <!-- end panel -->
