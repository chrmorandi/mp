
<?php
use yii\widgets\ListView;
use yii\helpers\Html;
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
<div class="panel-body">
  <div class="text-center">
    <?= Yii::t('frontend','Click');?> <?= Html::a('<span class="glyphicon glyphicon-plus" aria-hidden="true"></span><span class="glyphicon glyphicon-comment button-pad-left" aria-hidden="true"></span>', 'javascript:void(0);', ['class' => 'btn btn-primary mini-button mini-button-pad','title'=>'Add participants','onclick'=>'showNote();']); ?> <?= Yii::t('frontend','to send messages to others'); ?>
  </div>
</div>
<?php endif; ?>
