<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;
?>
<div id="noteMessage" class="alert-info alert fade in hidden">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<span id="noteMessage1"><?= Yii::t('frontend',"Thanks for your note. We'll automatically share it with other participants.")?></span>
<span id="noteMessage2"><?= Yii::t('frontend','Please be sure to type a note.')?></span>
</div>
<div class="panel panel-meeting">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingNote" >
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view"><?= Yii::t('frontend','Messages') ?></h4>
        <!-- <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNote" aria-expanded="true" aria-controls="collapseNote"> -->
        <span class="hint-text"><?= Yii::t('frontend','discuss the planning with others') ?></span>
      </div>
      <div class="col-lg-2 col-md-2 col-xs-2" >
        <div style="float:right;">
        <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-comment button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary','title'=>'Edit','onclick'=>'showNote();']); ?>
      </div>
      </div>
    </div>
  </div>
  <div id="collapseNote" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingNote">
    <div class="panel-body nopadding">
      <div id="editNote" class="hidden">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
      </div>
    </div>
    <div id ="noteThread" class="nopadding">
      <?= $this->render('_thread', [
          'model' => $model,
          'noteProvider'=>$noteProvider,
      ]) ?>
  </div>
</div>
</div>
