<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\HistoricalData */

$this->title = Yii::t('backend', 'Create Historical Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Historical Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historical-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
