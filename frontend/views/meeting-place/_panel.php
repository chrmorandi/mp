<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use \kartik\switchinput\SwitchInput;
use \common\components\MiscHelpers;
?>
<div id="notifierPlace" class="alert-info alert fade in">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <?php echo Yii::t('frontend',"We'll automatically notify others when you're done making changes."); ?>
</div>
<div class="panel panel-meeting">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingWhere">
    <div class="row">
      <div class="col-lg-8 col-md-6 col-xs-2" >
        <h4 class="meeting-place"><?= Yii::t('frontend','Where') ?></h4>
        <!-- <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhere" aria-expanded="true" aria-controls="collapseWhere"> -->
    </div>
<?php
  if (!$model->isOrganizer()) {
    // To Do: Check Meeting Settings whether participant can add places
  }
?>
      <div class="col-lg-4 col-md-6 col-xs-10" >
        <div id="virtualThingBox">
        <?php
          if ($model->isOrganizer() || $model->meetingSettings->participant_add_place) {
          ?>
          <table><tr style="vertical-align:top;"><td class="virtualThing" style="padding-left:10px;">
            <?php
            switch (Yii::$app->language) {
              case 'es':
                $handleWidth = 80;
              break;
              case 'fr':
              case 'ja':
                $handleWidth = 85;
              break;
              case 'ru':
                $handleWidth = 91;
              break;
              case 'fa':
                $handleWidth = 95;
              break;
              default:
                $handleWidth = 75;
              break;
            }
            echo SwitchInput::widget([
              'type' => SwitchInput::CHECKBOX,
              'name' => 'meeting-switch-virtual',
                'value' => $model->switchVirtual,
                'pluginOptions' => [
                  'handleWidth'=>$handleWidth,
                  'labelWidth'=>0,
                  'size'=>'small','onText' => '<i class="glyphicon glyphicon-user"></i>&nbsp;'.Yii::t('frontend','in person'),'offText'=>'<i class="glyphicon glyphicon-earphone"></i>&nbsp;'.Yii::t('frontend','virtual')], // 'onColor' => 'success','offColor' => 'danger'
                'labelOptions' => ['style' => 'font-size: 8px;'],
            ]);
            ?>
            </td><td style="padding-left:10px;">
            <?php
              if ($model->switchVirtual == $model::SWITCH_INPERSON) { ?>
                  <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-globe button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary button-margin-top','id'=>'meeting-add-place','aria-label'=>Yii::t('frontend','Add places'),'title'=>Yii::t('frontend','Add places'),'onclick'=>'showWherePlaces();']); ?>
                  <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary glyphicon glyphicon-star '.($userPlacesCount==0?'hidden ':' '),'id'=>'meeting-add-place-favorites','title'=>'Add favorite places','onclick'=>'showWhereFavorites();']); ?>
              <?php } else { ?>
                <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-globe button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['id'=>'meeting-add-place','class' => 'btn btn-primary button-margin-top','disabled'=>true,'onclick'=>'return false;']); ?>
                <?= Html::a('', 'javascript:void(0);', ['id'=>'meeting-add-place-favorites','class' => 'btn btn-primary glyphicon glyphicon-star','disabled'=>true,'onclick'=>'return false;']); ?>
              <?php } ?>
            </td></tr></table>
          <?php
          }
        ?>
        </div>
    </div>
  </div> <!-- end row -->
  <div class="row">
    <div class="col-lg-12 col-md-12 col-xs-12" >
  <div class="hint-text heading-pad">
    <?php if ($model->isOrganizer() || $model->meetingSettings['participant_choose_place']) {
        if ($placeProvider->count==0) {
          echo Yii::t('frontend','add possible meeting places or switch to \'virtual\'');
        } else if ($placeProvider->count==1)  {
          echo Yii::t('frontend','add possible meeting places or switch to \'virtual\'');
        } else if ($placeProvider->count>1) {
          echo Yii::t('frontend','add more places or decide the place');
        }
      } else {
        // not an organizer
        echo Yii::t('frontend','are these places acceptable?');
    }
    ?>
</div>
</div>
</div>
</div> <!-- end panel heading -->
  <div id="collapseWhere" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhere">
    <div class="panel-where">
      <div class="where-form hidden">
        <div id="placeMessage" class="alert-info alert fade in hidden">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <span id="placeMsg1"><?= Yii::t('frontend','We\'ll automatically notify others when you\'re done making changes.')?></span>
          <span id="placeMsg2"><?= Yii::t('frontend','Please select a place.')?></span>
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
      <div id="where-choices">
      <?php if ($placeProvider->count>=1 && ($model->isOrganizer() || $model->meetingSettings['participant_choose_place'])) { ?>
        <?= $this->render('../meeting-place/_choices', [
              'model'=>$model,
          ]);
           ?>
      <?php }?>
      </div> <!-- end where choices -->
    <div id="possible-places" class="panel-body <?= (($placeProvider->count==0 || $model->isOrganizer() )?'hidden':'') ?>" >
      <div class="row">
        <div class="col-xs-12" >
            <h5 id="available-places-msg" class="<?= ($placeProvider->count>1?'':'hidden') ?>"><?= Yii::t('frontend','Show Others Which Places You Prefer') ?></h5>
        </div>
      </div>
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
    <?= Html::hiddenInput('number_places',$placeProvider->count,['id'=>'number_places']); ?>
  </div> <!-- end possible-places -->
  </div> <!-- end meeting-place-list -->
  </div> <!-- end class panel-where -->
</div> <!-- end collapse panel where -->
</div> <!-- end panel -->
