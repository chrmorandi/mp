<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Request */

$this->title = Yii::t('frontend', 'Request a Change to Your Meeting');

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$model->meeting_id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'places' => $places,
        'times'=>$times,
        'altTimesList'=>$altTimesList,
        'countPlaces' => $countPlaces,
        'countTimes' => $countTimes,
        'currentStart' => $currentStart,
        'currentStartStr' => $currentStartStr,
    ]) ?>

</div>
