<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\Daemon;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\DaemonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Daemons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="daemon-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
     echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
          [
            'label'=>'Action',
              'attribute' => 'action_id',
              'format' => 'raw',
                'value' => function ($model) {
                          $d = new Daemon();
                          return '<div>'.$d->displayConstant($model->action_id).'</div>';
                  },
          ],
          [
            'label'=>'Task',
              'attribute' => 'task_id',
              'format' => 'raw',
                'value' => function ($model) {
                  $d = new Daemon();
                          return '<div>'.$d->displayConstant($model->task_id).'</div>';
                  },
          ],
          [
            'label'=>'Time',
              'attribute' => 'created_at',
              'format' => 'raw',
                'value' => function ($model) {
                          return '<div>'.\Yii::$app->formatter->asDatetime($model->created_at, "php:m-d H:i").'</div>';
                  },
          ],
        ],
    ]); ?>
</div>
