<?php

use yii\helpers\Html;
use yii\grid\GridView;
//use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Messages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Create Message'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php // Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
              // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
              'label'=>'Subject',
                'attribute' => 'subject',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div><a href="'.Url::to(['message/update', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-participant">'.$model->caption.'</span></div>';
                    },
            ],
            //'content:ntext',
            //'action_text',
            // 'action_url:url',
             //'status',
             [
               'label'=>'Recipients',
                 'attribute' => 'target',
                 'format' => 'raw',
                 'value' => function ($model) {
                      return '<div>'.$model->displayTarget().'</div>';
                     },
             ],
             [
               'label'=>'Status',
                 'attribute' => 'status',
                 'format' => 'raw',
                 'value' => function ($model) {
                     return '<div>'.$model->displayStatus().'</div>';
                     },
             ],
            // 'created_at',
            // 'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
            ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{update} {test} {next10} {next25} {next50} {next100} {trash} {view}',
            'headerOptions' => ['class' => 'itemHide'],
            'contentOptions' => ['class' => 'itemHide'],
            'buttons'=>[
                'update' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                          'title' => Yii::t('frontend', 'update'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'test' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-wrench"></span>', $url, [
                          'title' => Yii::t('frontend', 'test'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'trash' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                          'title' => Yii::t('frontend', 'delete'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this message?'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'next10' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-envelope"></span>', $url, [
                          'title' => Yii::t('frontend', 'send to next 10'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to deliver this message to the next 10 people?'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'next25' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-filter"></span>', $url, [
                          'title' => Yii::t('frontend', 'send to next 25'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to deliver this message to the next 25 people?'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'next50' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-equalizer"></span>', $url, [
                          'title' => Yii::t('frontend', 'send to next 50'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to deliver this message to the next 50 people?'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'next100' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-signal"></span>', $url, [
                          'title' => Yii::t('frontend', 'send to next 100'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to deliver this message to the next 100 people?'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'view' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['message-log/view', 'id' => $model->id]), [
                          'title' => Yii::t('frontend', 'view message log'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
              ]
            ],
        ],
    ]); ?>
<?php // Pjax::end(); ?></div>
