<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\PlaceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Your Places');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout'=>'{items}{summary}{pager}',
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($model) {
                            return '<div><a href="'.Url::to(['place/view', 'id' => $model->id]).'">'.$model->name.'</a></div>';
                    },
            ],[
                'attribute' => 'place_type',
                'headerOptions' => ['class' => 'itemHide'],
                'contentOptions' => ['class' => 'itemHide'],
                'format' => 'raw',
                'value' => function ($model) {
                            return '<div>'.$model->getPlaceType($model->place_type).'</div>';
                    },
            ],
            ['class' => 'yii\grid\ActionColumn',
				      'template'=>'{view} {update} ',
              'headerOptions' => ['class' => 'itemHide'],
              'contentOptions' => ['class' => 'itemHide'],
					    'buttons'=>[
                'view' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',$model->slug, ['title' => Yii::t('yii', 'View'),]);
						      }
							],
			      ],
        ],
    ]); ?>

    <p>
         <?= Html::a(Yii::t('frontend', 'Add {modelClass}', [
           'modelClass' => 'Place',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('frontend','Add via Google',[
           'modelClass' => 'Place'
        ]), ['create_place_google'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('frontend','Add My Location'), ['create_geo'], ['class' => 'btn btn-success vertical-pad']) ?>
    </p>

</div>
