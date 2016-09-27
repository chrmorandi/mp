
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
<?= Yii::t('frontend','No notes yet. ') ?>
<?= Html::a(Yii::t('frontend', 'Send a note to other participants.'), 'javascript:void(0);', ['class' => '','title'=>'Add a note','onclick'=>'showNote();']); ?>
</div>
<?php endif; ?>
