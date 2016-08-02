<?php
use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div id="notifierNote" class="alert-info alert fade in" style="display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<?php echo Yii::t('frontend',"We'll automatically notify the organizer when you're done making changes."); ?>
</div>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-xs-4"><h4 class="meeting-view"><?= Yii::t('frontend','Notes') ?></h4>
        <span class="hint-text"><?= Yii::t('frontend','send a message to other participants') ?></span>
      </div>
      <div class="col-lg-8 col-md-8 col-xs-8"><div style="float:right;"><?= Html::a(Yii::t('frontend', ''), ['meeting-note/create', 'meeting_id' => $model->id], ['class' => 'btn btn-primary  glyphicon glyphicon-plus']) ?>
      </div>
    </div>
  </div>
  </div>
  <?php
  if ($noteProvider->count>0):
  ?>
  <table class="table">
    <?= ListView::widget([
           'dataProvider' => $noteProvider,
           'itemOptions' => ['class' => 'item'],
           'layout' => '{items}',
           'itemView' => '_list',
       ]) ?>
  </table>

  <?php else: ?>
  <?php endif; ?>
</div>
