<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\Address;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\FriendSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Friends');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="friend-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li class="<?= ($tab=='friend'?'active':'') ?>"><a href="#friend" role="tab" data-toggle="tab">Friends</a></li>
      <li class="<?= ($tab=='address'?'active':'') ?>"><a href="#address" role="tab" data-toggle="tab">Contacts</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane  <?= ($tab=='friend'?'active':'') ?>" id="friend">

    <?= GridView::widget([
        'dataProvider' => $friendProvider,
        //'filterModel' => $friendSearchModel,
        'columns' => [
          'fullname',
          'email',
            ['class' => 'yii\grid\ActionColumn',
				      'template'=>'{delete}',
					    'buttons'=>[
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'delete/'.$model['id'], ['title' => Yii::t('yii', 'Delete'),]);
                }
							],
			      ],
        ],
    ]); ?>
      </div>

      <div class="tab-pane <?= ($tab=='address'?'active':'') ?>" id="address">
        <?= GridView::widget([
            'dataProvider' => $addressProvider,
            'filterModel'=>$addressSearchModel,
            'columns' => [
              'fullname',
              'email',
                ['class' => 'yii\grid\ActionColumn',
    				      'template'=>'{delete}',
    					    'buttons'=>[
                    'delete' => function ($url, $model) {
                      if ($model['address_type']=='f') {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'delete/'.$model['id'], ['title' => Yii::t('yii', 'Delete'),]);
                      } else {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 'addrdel/'.$model['id'], ['title' => Yii::t('yii', 'Delete'),]);
                      }

                    }
    							],
    			      ],
            ],
        ]); ?>

      </div>
    </div>

    <p>
        <?= Html::a(Yii::t('frontend', Yii::t('frontend','Add a Friend'), [
    'modelClass' => 'Friend',
    ]), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('frontend', Yii::t('frontend','Import Google Contacts'), [
        'modelClass' => 'Address',
      ]), ['/address/import'], ['class' => 'btn btn-success']); ?>
    </p>

</div>
