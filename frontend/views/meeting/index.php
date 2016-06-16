<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meetings');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->title ?></h1>

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#planning" role="tab" data-toggle="tab">Planning</a></li>
  <li ><a href="#upcoming" role="tab" data-toggle="tab">Confirmed</a></li>
  <li class="tabHide"><a href="#past" role="tab" data-toggle="tab" >Past</a></li>
  <li class="tabHide"><a href="#canceled" role="tab" data-toggle="tab" class="itemHide">Canceled</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="planning">
    <div class="meeting-index">
      <?php
      ?>
      <?= $this->render('_grid', [
          'mode'=>'planning',
          'dataProvider' => $planningProvider,
      ]) ?>

    </div> <!-- end of planning meetings tab -->
  </div>
  <div class="tab-pane" id="upcoming">
    <div class="meeting-index">
      <?php
      ?>
      <?= $this->render('_grid', [
          'mode'=>'upcoming',
          'dataProvider' => $upcomingProvider,
      ]) ?>

      </div> <!-- end of upcoming meetings tab -->
  </div>
  <div class="tab-pane" id="past">

    <?= $this->render('_grid', [
        'mode'=>'past',
        'dataProvider' => $pastProvider,
    ]) ?>
  </div> <!-- end of past meetings tab -->
  <div class="tab-pane" id="canceled">
    <?= $this->render('_grid', [
        'mode'=>'canceled',
        'dataProvider' => $canceledProvider,
    ]) ?>

  </div> <!-- end of canceled meetings tab -->

</div>
