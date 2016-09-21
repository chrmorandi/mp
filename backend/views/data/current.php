<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use common\models\User;
use frontend\models\Meeting;
$this->title = Yii::t('backend','Meeting Planner');
?>
<div class="site-index">
  <div class="body-content">

        <h1>Real Time Data</h1>
          <h3>Meetings</h3>

        <?= GridView::widget([
            'dataProvider' => $data->meetings,
            'columns' => [
              [
                'label'=>'Status',
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                    return '<div>'.Meeting::lookupStatus($model->status).'</div>';
                      },
              ],
              'dataCount',
            ],
        ]); ?>

<h3>People</h3>
<p><strong>Total users: </strong> <?= $data->totalUsers ?></p>
        <?= GridView::widget([
            'dataProvider' => $data->users,
            'columns' => [
              [
                'label'=>'Status',
                  'attribute' => 'status',
                  'format' => 'raw',
                  'value' => function ($model) {
                    return '<div>'.User::lookupStatus($model->status).'</div>';
                      },
              ],
              'dataCount',
            ],
        ]); ?>

<h3>Places</h3>
<p>Currently averaging <?= sprintf("%.1f", $data->avgUserPlaces) ?> places per user</p>
<p><em>Users with most places below</em></p>
<?= GridView::widget([
    'dataProvider' => $data->userPlaces,
    'columns' => [
      'user_id',
      'dataCount',
    ],
]); ?>


</div>
