<?php
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\bootstrap\Collapse;
?>
<div id="notifierNote" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading" role="tab" id="headingNote" >
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNote" aria-expanded="true" aria-controls="collapseNote"><?= Yii::t('frontend','Notes') ?></a></h4>
        <span class="hint-text"><?= Yii::t('frontend','send a message to others') ?></span>
      </div>
      <div class="col-lg-2 col-md-2 col-xs-2" ><div style="float:right;"><?= Html::a(Yii::t('frontend', ''), ['meeting-note/create', 'meeting_id' => $model->id], ['class' => 'btn btn-primary  glyphicon glyphicon-plus']) ?>
      </div>
    </div>
  </div>
  </div>
  <div id="collapseNote" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingNote"  >
    <div class="panel-body nopadding">
      <?php
      if ($noteProvider->count>0):
      ?>
      <table class="table">
        <?= ListView::widget([
               'dataProvider' => $noteProvider,
               //'itemOptions' => ['class' => 'item'],
               'layout' => '{items}',
               'itemView' => '_list',
           ]) ?>
      </table>
      <?php else: ?>
        <?= Yii::t('frontend','No notes yet.') ?>
        <?= Html::a(Yii::t('frontend', 'Send a note to other participants.'), ['meeting-note/create', 'meeting_id' => $model->id]); ?>
      <?php endif; ?>
    </div>
  </div>
</div>
