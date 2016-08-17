<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use frontend\models\Request;
use common\components\MiscHelpers;


/* @var $this yii\web\View */
/* @var $searchModel frontend\models\RequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Requests');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$meeting_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-index">
  <div class="row">
    <div class="col-xs-6">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
          [
            'label'=>'Request details',
              'attribute' => 'id',
              'format' => 'raw',
              'value' => function ($model) {
                    if (Yii::$app->user->getId() == $model->requestor_id) {
                      $url = Url::to(['view','id'=>$model->id]);
                    } else {
                      $url = Url::to(['/request-response/create','id'=>$model->id]);
                    }
                      return '<div>'.Html::a(Request::buildSubject($model->id),$url).'</div>';
                  },
          ],
            //'time_adjustment:datetime',
            //'alternate_time',
            // 'meeting_time_id:datetime',
            // 'place_adjustment',
             //'meeting_place_id',
            // 'status',
            /*
            ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{accept} {reject}',
            'buttons'=>[
                'accept' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-thumbs-up"></span>', $url, [
                          'title' => Yii::t('frontend', 'accept'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
                'reject' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-thumbs-down"></span>', $url, [
                          'title' => Yii::t('frontend', 'reject'),
                          'class' => 'icon-pad admin-pad',
                  ]);
                },
              ]
            ],
            */
        ],
    ]); ?>
    <p>
        <?= Html::a(Yii::t('frontend', 'Create a New Request'), ['create','meeting_id'=>$meeting_id], ['class' => 'btn btn-success']) ?>
    </p>
  </div>
  </div>
</div>
