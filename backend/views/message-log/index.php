<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\MiscHelpers;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MessageLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Message Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'message_id',
            'user_id',
            [
              'label'=>'Actor',
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {
                        return '<div>'.MiscHelpers::getDisplayName($model->user_id).'</div>';
                    },
            ],
            [
              'label'=>'Response',
                'attribute' => 'response',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div>'.$model->displayResponse().'</div>';
                    },
            ],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
