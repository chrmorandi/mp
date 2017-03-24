<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\Place;

$this->title = Yii::t('backend','Meeting Planner');
?>
<div class="site-index">
  <div class="body-content">
        <h1>Real Time Meeting Data</h1>
        <em>All data below is based on confirmed and completed meetings only.</em>
        <h3><?=  Yii::t('frontend','Meeting Types') ?></h3>
        General: <?= number_format($data->no_activity,2) ?> <br />
        Activities: <?= number_format($data->activities,2) ?> <br />
        Total: <?= number_format($data->total,2) ?> <br />

        <h3><?=  Yii::t('frontend','Average Number Per Meeting') ?></h3>
        Times: <?= number_format($data->avgTimes,2) ?> <br />
        Places: <?= number_format($data->avgPlaces,2) ?> <br />


        <h3>Number of Meetings Created By Organizers</h3>
        <?= GridView::widget([
          'dataProvider' => $data->owner,
          'columns' => [
            [
              'label'=>'Number of Participants',
                'attribute' => 'owner_id',
                'format' => 'raw',
                'value' => function ($model) {                  
                  return '<div>'.MiscHelpers::getDisplayName($model->owner_id).'-'.$model->owner_id.'</div>';
                    },
            ],
            'cnt',
          ],
        ]); ?>

        <h3>Frequency of Number of Participants in Meetings</h3>

        <?= GridView::widget([
          'dataProvider' => $data->participants,
          'columns' => [
            [
              'label'=>'Number of Participants',
                'attribute' => 'count_participants',
                'format' => 'raw',
                'value' => function ($model) {
                  return '<div>'.$model->count_participants.'</div>';
                    },
            ],
            'cnt',
          ],
        ]); ?>
          <h3>Frequency of Day of Week</h3>

        <?= GridView::widget([
            'dataProvider' => $data->dwCount,
            'columns' => [
              [
                'label'=>'Day of Week',
                  'attribute' => 'dayweek',
                  'format' => 'raw',
                  'value' => function ($model) {
                    return '<div>'.MiscHelpers::getDayOfWeek($model->dayweek).'</div>';
                      },
              ],
              'cnt',
            ],
        ]); ?>

        <h3>Frequency of Hour of Day</h3>
        <?= GridView::widget([
            'dataProvider' => $data->hourofday,
            'columns' => [
              [
                'label'=>'Time of Day',
                  'attribute' => 'hour',
                  'format' => 'raw',
                  'value' => function ($model) {
                    return '<div>'.date('g a',strtotime('today midnight')+($model->hour*3600)).'</div>';
                      },
              ],
              'cnt',
            ],
        ]); ?>
        <h3>Frequency of Places Used in Meetings</h3>

        <?= GridView::widget([
          'dataProvider' => $data->places,
          'columns' => [
            [
              'label'=>'Place',
                'attribute' => 'chosen_place_id',
                'format' => 'raw',
                'value' => function ($model) {
                  return '<div>'.Place::findOne($model->chosen_place_id)->name.'</div>';
                    },
            ],
            'cnt',
          ],
        ]); ?>

        <h3>User Distribution by Timezone</h3>
        <?= GridView::widget([
          'dataProvider' => $data->user_tz,
          'columns' => [
            [
              'label'=>'Timezones',
                'attribute' => 'timezone',
                'format' => 'raw',
                'value' => function ($model) {
                  return '<div>'.$model->timezone.'</div>';
                    },
            ],
            'cnt',
          ],
        ]); ?>

</div>
