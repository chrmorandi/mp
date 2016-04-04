<?php
  use yii\helpers\Html;
  use yii\grid\GridView;
  use yii\helpers\Url;
?>
<p></p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    //'filterModel' => $searchModel,
    'columns' => [
    [
      'label'=>'Description',
        'attribute' => 'meeting_type',
        'format' => 'raw',
        'value' => function ($model) {                      
                    return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a></div>';
            },
    ],
    [
      'label'=>'Last updated',
        'attribute' => 'updated_at',
        'format' => 'raw',
        'value' => function ($model) {                      
                    return '<div>'.Yii::$app->formatter->asDatetime($model->updated_at,"MMM d").'</div>';
            },
    ],
    // to do: make this conditional for tabs, show delete on cancel tab, no cancel on past tab
        ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {cancel}',
        'buttons'=>[
            'view' => function ($url, $model) {     
              return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                      'title' => Yii::t('yii', 'view'),
              ]);                                
            },
            'cancel' => function ($url, $model) {     
              return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                      'title' => Yii::t('yii', 'cancel'),
              ]);                                
            }
                                      
          ]
        ],
    ],
]); ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'Meeting',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>