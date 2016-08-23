<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use frontend\models\Meeting;
use common\models\User;
use common\components\MiscHelpers;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meeting History for '.$subject);
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(); ?>
<div class="meeting-log-index">
    <h1><?php echo  Html::encode($this->title) ?></h1>

    <?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
        [
          'label'=>'Actor',
            'attribute' => 'actor_id',
            'format' => 'raw',
            'value' => function ($model) {
                    return '<div>'.MiscHelpers::getDisplayName($model->actor_id).'</div>';
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
        [
          'label'=>'Item',
            'attribute' => 'item_id',
            'format' => 'raw',
            'value' => function ($model) {
                        return '<div>'.$model->getMeetingLogItem().'</div>';
                },
        ],
        [
          'label'=>'Created',
            'attribute' => 'created_at',
            'format' => 'raw',
            'value' => function ($model) {
                        return '<div>'.Yii::$app->formatter->asDatetime($model->created_at,"hh:ss MMM d").'</div>';
                },
        ],
    ],
]);
?>
<?= Html::a(Yii::t('frontend', 'Return to Meeting'), ['meeting/view', 'id' => $meeting_id],
 ['class' => 'btn btn-primary  btn-info',
 'title'=>Yii::t('frontend','Return to meeting page'),
]); ?>
<?php Pjax::end(); ?>

</div>
