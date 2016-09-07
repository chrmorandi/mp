<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;
?>
<div id="notifierNote" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"Thanks for your note. We'll automatically share it with other participants."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingNote" >
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNote" aria-expanded="true" aria-controls="collapseNote"><?= Yii::t('frontend','Notes') ?></a></h4>
        <span class="hint-text"><?= Yii::t('frontend','send a message to others') ?></span>
      </div>
      <div class="col-lg-2 col-md-2 col-xs-2" >
        <div style="float:right;">
        <?= Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary glyphicon glyphicon-plus','title'=>'Edit','onclick'=>'showNote();']); ?>
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
