<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */

$this->title = 'Create Place By Geolocation';
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Your Places'), 'url' => ['yours']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-create">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_formGeolocate', [
        'model' => $model,
    ]) ?>

</div>


<?php
$gpJsLink= 'https://maps.googleapis.com/maps/api/js?' . http_build_query([
                        'key' => Yii::$app->params['google_maps_key'],
    ]);
$this->registerJsFile($gpJsLink);
 ?>
