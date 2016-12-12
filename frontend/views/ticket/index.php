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
        'dataProvider' => $ticketProvider,
        //'filterModel' => $searchModel,
        'columns' => [
          [
            'label'=>'Subject',
              'attribute' => 'subject',
              'format' => 'raw',
              'value' => function ($model) {
                return '<div>'.Html::a($model->subject,['ticket/view/','id'=>$model->id]).'</div>';
                  },
          ],
            [
              'label'=>'Status',
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                  return '<div>'.$model->getStatus().'</div>';
                    },
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<p>
    <?= Html::a(Yii::t('frontend', 'Create a New Ticket'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
