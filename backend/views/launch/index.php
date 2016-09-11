<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Launches');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="launch-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'email:email',
            'ip_addr',
            'status',
            [
              'label'=>'Date',
                'attribute' => 'created_at',
                'format' => ['date', 'php:m/d/y'],
              ],
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
