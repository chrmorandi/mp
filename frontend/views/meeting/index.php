<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if (Yii::$app->params['site']['id'] == \common\components\SiteHelper::SITE_MP) {
  $this->title= Yii::t('frontend', 'Meetings');
} else {
  $this->title = Yii::t('frontend', 'Meetups');
}
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->title ?></h1>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="<?= ($tab=='planning'?'active':'inactive') ?>"><a href="#planning" role="tab" data-toggle="tab"><?= Yii::t('frontend','Planning') ?></a></li>
  <li class="<?= ($tab=='upcoming'?'active':'inactive') ?>"><a href="#upcoming" role="tab" data-toggle="tab"><?= Yii::t('frontend','Confirmed') ?></a></li>
  <li class="<?= ($tab=='past'?'active':'inactive') ?>"><a href="#past" role="tab" data-toggle="tab" ><?= Yii::t('frontend','Past') ?></a></li>
  <li class="<?= ($tab=='canceled'?'active':'inactive') ?>"><a href="#canceled" role="tab" data-toggle="tab"><?= Yii::t('frontend','Canceled') ?></a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane <?= ($tab=='planning'?'active':'') ?>" id="planning">
    <div class="meeting-index">
      <?= $this->render('_grid', [
          'mode'=>'planning',
          'dataProvider' => $planningProvider,
          'timezone'=>$timezone,
      ]) ?>

    </div> <!-- end of planning meetings tab -->
  </div>
  <div class="tab-pane <?= ($tab=='upcoming'?'active':'') ?>" id="upcoming">
    <div class="meeting-index">
      <?= $this->render('_grid', [
          'mode'=>'upcoming',
          'dataProvider' => $upcomingProvider,
          'timezone'=>$timezone,
      ]) ?>

      </div> <!-- end of upcoming meetings tab -->
  </div>
  <div class="tab-pane <?= ($tab=='past'?'active':'') ?>" id="past">
    <?= $this->render('_grid', [
        'mode'=>'past',
        'dataProvider' => $pastProvider,
        'timezone'=>$timezone,
    ]) ?>
  </div> <!-- end of past meetings tab -->
  <div class="tab-pane <?= ($tab=='canceled'?'active':'') ?>" id="canceled">
    <?= $this->render('_grid', [
        'mode'=>'canceled',
        'dataProvider' => $canceledProvider,
        'timezone'=>$timezone,
    ]) ?>

  </div> <!-- end of canceled meetings tab -->

</div>
