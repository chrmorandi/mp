<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Support Tickets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            'question:ntext',
            'status',
            ['class' => 'yii\grid\ActionColumn','header'=>Yii::t('frontend','Options')],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<p>
    <?= Html::a(Yii::t('frontend', 'Create Ticket'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
