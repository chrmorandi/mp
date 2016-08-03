<?php
use yii\helpers\Html;
use yii\widgets\ListView;
?>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"><div class="row"><div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view"><?= Yii::t('frontend','Who') ?></h4><span class="hint-text"><?= Yii::t('frontend','add participants via email or from your friends list') ?></span></div>
  <div class="col-lg-2 col-md-2 col-xs-2"><div style="float:right;"><?= Html::a(Yii::t('frontend', ''), ['/participant/create', 'meeting_id' => $model->id], ['class' => 'btn btn-primary  glyphicon glyphicon-plus'.((!empty($participantProvider) and ($participantProvider->getCount()>0))?' disabled':'')]) ?></div></div></div></div>
    <?php
    if (!empty($participantProvider) and $participantProvider->getCount()>0):
    ?>
    <table class="table">
      <?= ListView::widget([
             'dataProvider' => $participantProvider,
             'itemOptions' => ['class' => 'item'],
             'layout' => '{items}',
             'itemView' => '_list',
         ]) ?>
    </table>

    <?php else: ?>
    <?php endif; ?>

</div>
