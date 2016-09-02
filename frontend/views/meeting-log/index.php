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

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$meeting_id]];
$this->title = Yii::t('frontend', 'History for '.$subject);
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
        'label'=>'Description',
          'attribute' => 'actor_id',
          'format' => 'raw',
          'value' => function ($model) {
                  $command = $model->getMeetingLogCommand();
                  /*if ($command == 'Sent' || $command =='Finalized') {
                    $actor = 'Meeting';
                  } else {
                  }*/
                  $actor = MiscHelpers::getDisplayName($model->actor_id);
                  $item = $model->getMeetingLogItem();
                  if ($item == '-') {
                    $item ='';
                  }
                  return '<div>'
                  .$actor
                  .' '.$command
                  .' '.$item
                  .'</div>';
              },
      ],
        [
          'label'=>'When',
          'headerOptions' => ['class' => 'itemHide'],
          'contentOptions' => ['class' => 'itemHide'],          
            'attribute' => 'created_at',
            'format' => 'raw',
            'options' => ['class'=>'itemHide'],
            'value' => function ($model) {
                        return '<div>'.Yii::$app->formatter->asDatetime($model->created_at,"MMM d, h:ss a").'</div>';
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
