<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use frontend\models\Meeting;
use common\models\User;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meeting Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // = Html::a(Yii::t('frontend', 'Create Meeting Log'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'meeting_id',
            [
              'label'=>'Subject',
                'attribute' => 'subject',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div>'.Meeting::getSubject($model->meeting_id).'</div>';
                    },
            ],
            [
              'label'=>'Actor',
                'attribute' => 'actor_id',
                'format' => 'raw',
                'value' => function ($model) {
                            return '<div>'.User::find($model->actor_id)->one()->username.'</div>';
                    },
            ],
            [
              'label'=>'Action',
                'attribute' => 'action',
                'format' => 'raw',
                'value' => function ($model) {
                            return '<div>'.$model->getMeetingLogCommand().'</div>';
                    },
            ],
            'item_id',
            // 'extra_id',
            [
              'label'=>'Created',
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                            return '<div>'.Yii::$app->formatter->asDatetime($model->created_at,"hh:ss MMM d").'</div>';
                    },
            ],
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
