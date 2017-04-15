<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Reminders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'layout'=>'{items}{pager}{summary}',
        'options' => ['class'=>'vertical-pad'],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
              'label'=>Yii::t('frontend','Time before meeting'),
                'attribute' => 'duration_friendly',
                'format' => 'raw',
                'value' => function ($model) {
                        return '<div><a href="'.Url::to(['reminder/update', 'id' => $model->id]).'">'.$model->duration_friendly.' '.$model->displayUnits($model->unit).'&nbsp;'.Yii::t('frontend','via').'&nbsp;'.$model->displayType($model->reminder_type).'</a></div>';
                    },
            ],
            ['class' => 'yii\grid\ActionColumn','header'=>Yii::t('frontend','Options'),'template'=>'{update}  {delete}',
            'headerOptions' => ['class' => 'itemHide'],
            'contentOptions' => ['class' => 'itemHide'],
            'buttons'=>[
                'update' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,
                  [
                          'title' => Yii::t('frontend', 'update'),
                          'class' => 'icon-pad',
                  ]);
                },
                'delete' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                          'title' => Yii::t('frontend', 'delete'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this reminder?'),
                          'class' => 'icon-pad',
                          'method' => 'post',
                  ]);
                },
            ],
          ],
        ],
    ]); ?>
<p>
    <?= Html::a(Yii::t('frontend', 'Add a Reminder'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
