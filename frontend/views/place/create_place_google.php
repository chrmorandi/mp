<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */

$this->title = 'Create Place from Google Places';
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Your Places'), 'url' => ['place/yours']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-create">

    <h1><?php echo  Html::encode($this->title) ?></h1>
    <?php echo  $this->render('_formPlaceGoogle', [
        'model' => $model,
    ]) ?>

</div>
<?php
  $gpJsLink= 'https://maps.googleapis.com/maps/api/js?' . http_build_query([
                          'key' => Yii::$app->params['google_maps_key'],
                          'libraries' => 'places',
      ]);
   $this->registerJsFile($gpJsLink);

  $options = '{"types":["establishment"]}';
    // off ,"componentRestrictions":{"country":"us"}
  echo $this->registerJs("(function(){
        var input = document.getElementById('place-searchbox');
        var options = $options;
        searchbox = new google.maps.places.Autocomplete(input, options);
        setupListeners('place');
})();" , \yii\web\View::POS_END );
//setupBounds('.$bound_bl.','.$bound_tr.');
?>
