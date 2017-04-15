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
    <ul class="nav nav-tabs vertical-pad" role="tablist">
      <li class="<?= ($tab=='friend'?'active':'') ?>"><a href="#friend" role="tab" data-toggle="tab"><?= Yii::t('frontend','Friends');?></a></li>
      <li class="<?= ($tab=='address'?'active':'') ?>"><a href="#address" role="tab" data-toggle="tab"><?= Yii::t('frontend','Contacts');?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane <?= ($tab=='friend'?'active':'') ?>" id="friend">

    <?= GridView::widget([
        'dataProvider' => $friendProvider,
        //'filterModel' => $friendSearchModel,
        'layout'=>'{items}{summary}{pager}',
        'headerRowOptions' => ['class'=>'hidden'],
        'columns' => [
          [
            'contentOptions' => ['class' => 'col-lg-11 col-xs-10'],
            'label'=>'Name',
              'attribute' => 'fullname',
              'format' => 'raw',
              'value' => function ($model) {
                  if (trim($model['fullname'])=='') {
                    return '<div>'.$model['email'].'</div>';
                  } else {
                      return '<div>'.$model['fullname'].' &lt;'.$model['email'].'&gt;</div>';
                  }
                  },
          ],
            ['class' => 'yii\grid\ActionColumn',
				      'template'=>'{delete}',
					    'buttons'=>[
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete','id'=>$model['id']],
                      ['title' => Yii::t('yii', 'Delete'),
                      'data' => [
                        'confirm' => Yii::t('frontend', 'Are you sure you want to delete this friend?'),
                        'method' => 'post',],
                    'class' => 'icon-pad']
                    );
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
            'headerRowOptions' => ['class'=>'hidden'],
            'layout'=>'{items}{pager}{summary}',
            'columns' => [
            [
              'contentOptions' => ['class' => 'col-lg-11 col-xs-10'],
              'label'=>'Name',
                'attribute' => 'fullname',
                'format' => 'raw',
                'value' => function ($model) {
                    if (trim($model->fullname)=='') {
                          return '<div>'.$model->email.'</div>';
                    } else {
                          return '<div>'.$model->fullname.' &lt;'.$model->email.'&gt;</div>';
                    }
                  },
            ],
                ['class' => 'yii\grid\ActionColumn',
    				      'template'=>'{delete}',
    					    'buttons'=>[
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['address/delete','id'=>$model['id']],
                          ['title' => Yii::t('yii', 'Delete'),
                          'data' => [
                            'confirm' => Yii::t('frontend', 'Are you sure you want to delete this contact?'),
                            'method' => 'post',],
                        'class' => 'icon-pad']
                        );
                    }
    							],
    			      ],
            ],
        ]); ?>

      </div>
    </div>

    <p>
        <?= Html::a(Yii::t('frontend','Add a Friend', [
    'modelClass' => 'Friend',
    ]), ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a(Yii::t('frontend', 'Import Google Contacts', [
        'modelClass' => 'Address',
      ]), ['/address/import'], ['class' => 'btn btn-success']); ?>
    </p>

</div>
