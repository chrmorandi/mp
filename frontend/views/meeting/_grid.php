<?php
  use yii\helpers\Html;
  use yii\grid\GridView;
  use yii\helpers\Url;
?>
<p></p>
<?php
// different tabs display the date in grid form separately
if ($mode =='upcoming' || $mode =='past') {
      echo GridView::widget([
          'dataProvider' => $dataProvider,
          //'filterModel' => $searchModel,
          'columns' => [
          [
            'label'=>'Subject',
              'attribute' => 'meeting_type',
              'format' => 'raw',
              'value' => function ($model) {
                  // to do - remove legacy code when subject didn't exist
                    if ($model->subject=='') {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a></div>';
                    } else {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a></div>';
                    }
                  },
          ],
          [
            'label'=>'Participant(s)',
              'attribute' => 'id',
              'format' => 'raw',
              'value' => function ($model) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingParticipants($model->id).'</a></div>';
                  },
          ],
          /*[
            'label'=>'Type',
              'attribute' => 'meeting_type',
              'format' => 'raw',
              'value' => function ($model) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingType($model->meeting_type).'</a></div>';
                  },
          ],*/
          [
            'label'=>'Date',
              'attribute' => 'created_at',
              'format' => 'raw',
              'value' => function ($model) {
                    $chosenTime = $model->getChosenTime($model->id);
                    return '<div>'.Yii::$app->formatter->asDatetime($chosenTime->start,"MMM d").'</div>';
                  },
          ],
              ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view}  {decline}  {cancel}',
              'buttons'=>[
                  'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'title' => Yii::t('frontend', 'view'),
                    ]);
                  },
                  'decline' => function ($url, $model) {
                    return ($model->status==$model::STATUS_SENT ) ? Html::a('<span class="glyphicon glyphicon-thumbs-down"></span>', $url, [
                            'title' => Yii::t('frontend', 'decline')
                    ]) : '';
                  },
                  'cancel' => function ($url, $model) {
                    return ($model->status==$model::STATUS_SENT || $model->status==$model::STATUS_CONFIRMED ) ? Html::a('<span class="glyphicon glyphicon-remove-circle"></span>', $url, [
                            'title' => Yii::t('frontend', 'cancel'),
                            'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
                    ]) : '';
                  },
                ]
              ],
          ],
      ]);
} else {
  // mode is planning or canceled
  echo GridView::widget([
      'dataProvider' => $dataProvider,
      //'filterModel' => $searchModel,
      'columns' => [
      [
        'label'=>'Subject',
          'attribute' => 'meeting_type',
          'format' => 'raw',
          'value' => function ($model) {
              // to do - remove legacy code when subject didn't exist
                if ($model->subject=='') {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a></div>';
                } else {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a></div>';
                }
              },
      ],
      [
        'label'=>'Participant(s)',
          'attribute' => 'id',
          'format' => 'raw',
          'value' => function ($model) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingParticipants($model->id).'</a></div>';
              },
      ],
      /*[
        'label'=>'Type',
          'attribute' => 'meeting_type',
          'format' => 'raw',
          'value' => function ($model) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingType($model->meeting_type).'</a></div>';
              },
      ],*/
      [
        'label'=>'Created',
          'attribute' => 'created_at',
          'format' => 'raw',
          'value' => function ($model) {
                return '<div>'.Yii::$app->formatter->asDatetime($model->created_at,"MMM d").'</div>';
              },
      ],
          ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {trash}',
          'buttons'=>[
              'view' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('frontend', 'view'),
                ]) ;
              },
              'trash' => function ($url, $model) {
                return $model->status==$model::STATUS_PLANNING ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('frontend', 'delete'),
                        'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this meeting?')
                ]) :'';
              },
            ]
          ],
      ],
  ]);

}
?>

    <p>
        <?= Html::a(Yii::t('frontend', Yii::t('frontend','Schedule a Meeting'), [
    'modelClass' => 'Meeting',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
