<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ReminderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Reminders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
              'label'=>'Time Before',
                'attribute' => 'duration_friendly',
                'format' => 'raw',
                'value' => function ($model) {
                        return '<div>'.$model->duration_friendly.' '.$model->displayUnits($model->unit).'</div>';
                    },
            ],
            [
              'label'=>'Delivery',
                'attribute' => 'reminder_type',
                'format' => 'raw',
                'value' => function ($model) {
                        return '<div>via '.$model->displayType($model->reminder_type).'</div>';
                    },
            ],
            ['class' => 'yii\grid\ActionColumn',
              'template'=>'{update}&nbsp;&nbsp;{delete}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<p>
    <?= Html::a(Yii::t('frontend', 'Add a Reminder'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
