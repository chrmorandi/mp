<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\UserContact;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\UserContactSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Your Contact Information');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contact-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Yii::t('frontend','You can add phone numbers and video conferencing addresses to share with your meeting contacts. e.g. Skype. Only add contacts that you wish to share with meeting participants.'); ?></p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'headerRowOptions' => ['class'=>'hidden'],
        'layout'=>'{items}{pager}{summary}',
        'options' => ['class'=>'vertical-pad'],
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
		        [
		            'attribute' => Yii::t('frontend','Type'),
		            'format' => 'raw',
		            'value' => function ($model) {
		                        return '<div>'.$model->getUserContactType($model->contact_type).'</div>';
		                },
		        ],
            [
		            'attribute' => Yii::t('frontend','Information'),
		            'format' => 'raw',
		            'value' => function ($model) {
		                        return '<div>'.$model->info.'</div>';
		                },
		        ],
            // 'status',
            ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{verify}  {update} {delete}',
            'headerOptions' => ['class' => 'itemHide'],
            'contentOptions' => ['class' => 'itemHide'],
            'buttons'=>[
                'verify' => function ($url, $model) {
                  if ($model->contact_type == UserContact::TYPE_PHONE
                    && $model->status==UserContact::STATUS_ACTIVE) {

                  return Html::a('<span class="glyphicon glyphicon-check"></span>', $url,
                  [
                          'title' => Yii::t('frontend', 'verify'),
                          'class' => 'icon-pad',
                  ]);
                }

                },
                /*'cancel' => function ($url, $model) {
                  return ($model->status==$model::STATUS_SENT || $model->status==$model::STATUS_CONFIRMED ) ? Html::a('<span class="glyphicon glyphicon-remove-circle"></span>', $url, [
                          'title' => Yii::t('frontend', 'cancel'),
                          'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?'),
                          'class' => 'icon-pad',
                  ]) : '';
                },*/
              ]
            ],
        ],
    ]); ?>
    <p>
        <?= Html::a(Yii::t('frontend', 'Add Contact Details', [
    'modelClass' => 'User Contact',
    ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
