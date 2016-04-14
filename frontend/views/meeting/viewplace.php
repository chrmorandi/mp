<?php

use dosamigos\google\maps\Map;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\Marker;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */
$this->title = $model->getMeetingHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $place->name;
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
  <?= Html::a(Yii::t('frontend', 'Return to Meeting'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
</p>

<div class="col-md-6">
<div class="place-view">

    <?= DetailView::widget([
        'model' => $place,
        'attributes' => [
            'name',
            'place_type',
            'website',
            'full_address',
        ],
    ]) ?>

</div>
</div> <!-- end first col -->
<div class="col-md-6">
  <?
  if ($gps!==false) {
    $coord = new LatLng(['lat' => $gps->lat, 'lng' => $gps->lng]);
    $map = new Map([
        'center' => $coord,
        'zoom' => 14,
        'width'=>300,
        'height'=>300,
    ]);
    $marker = new Marker([
        'position' => $coord,
        'title' => $place->name,
    ]);
    // Add marker to the map
    $map->addOverlay($marker);
    echo $map->display();
  } else {
    echo 'No location coordinates for this place could be found.';
  }
  ?>

</div> <!-- end second col -->
