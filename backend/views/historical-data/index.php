<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\HistoricalDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Historical Datas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historical-data-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('backend', 'Create Historical Data'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date',
            'percent_own_meeting',
            'percent_own_meeting_last30',
            'percent_invited_own_meeting',
            // 'percent_participant',
            // 'count_users',
            // 'count_meetings_completed',
            // 'count_meetings_planning',
            // 'count_places',
            // 'average_meetings',
            // 'average_friends',
            // 'average_places',
            // 'source_google',
            // 'source_facebook',
            // 'source_linkedin',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
