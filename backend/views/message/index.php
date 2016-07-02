<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
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
<?php Pjax::begin(); ?>    <?= GridView::widget([
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
             'status',
            // 'created_at',
            // 'updated_at',

            //['class' => 'yii\grid\ActionColumn'],
            ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{update} {trash}',
            'headerOptions' => ['class' => 'itemHide'],
            'contentOptions' => ['class' => 'itemHide'],
            'buttons'=>[
                'update' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                          'title' => Yii::t('frontend', 'update'),
                          'class' => 'icon-pad',
                  ]);
                },
                'trash' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                          'title' => Yii::t('frontend', 'delete'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this message?'),
                          'class' => 'icon-pad',
                  ]);
                },
              ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
