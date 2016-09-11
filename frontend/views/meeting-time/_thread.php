
<?php
use yii\widgets\ListView;
use yii\helpers\Html;
if ($timeProvider->count>0):
?>
<table class="table">
  <?= ListView::widget([
         'dataProvider' => $timeProvider,
         //'itemOptions' => ['class' => 'item'],
         'layout' => '{items}',
         'itemView' => '_list',
     ]) ?>
</table>
<?php endif; ?>
