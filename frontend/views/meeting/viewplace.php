<?php

use dosamigos\google\maps\Map;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\Marker;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */
$this->title = $place->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?php echo Html::encode($this->title) ?></h1>


<div class="col-xs-12 col-lg-6">
<div class="place-view">

    <?php echo DetailView::widget([
        'model' => $place,
        'attributes' => [

            ['label' => Yii::t('frontend','Website'),
     'value' => Html::a($place->website, $place->website),
     'format' => 'raw'],
            'full_address',
        ],
    ]) ?>

</div>
</div> <!-- end first col -->
<div class="col-xs-12 col-lg-6">
  <?php
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

<p>
  <?= Html::a(Yii::t('frontend', 'Return to Meeting'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
  <?= Html::a(Yii::t('frontend', 'Remove Place'), ['removeplace', 'meeting_id'=>$model->id,'place_id' => $place->id], ['class' => 'btn btn-danger',
  'data' => [
      'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
      'method' => 'post',
  ],]) ?>
</p>
