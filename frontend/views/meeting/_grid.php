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
            'label'=>'Details',
              'attribute' => 'meeting_type',
              'format' => 'raw',
              'value' => function ($model) {
                  // to do - remove legacy code when subject didn't exist
                    if ($model->subject=='' || $model->subject==$model::DEFAULT_SUBJECT) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a><br /><span class="index-participant">'.$model->getMeetingParticipants($model->id).'</span></div>';
                    } else {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-participant">'.$model->getMeetingParticipants($model->id).'</span></div>';
                    }
                  },
          ],
          /*
          [
            'label'=>'Participant(s)',
              'attribute' => 'id',
              'format' => 'raw',
              'value' => function ($model) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingParticipants($model->id).'</a></div>';
                  },
          ],
          [
            'label'=>'Type',
              'attribute' => 'meeting_type',
              'format' => 'raw',
              'value' => function ($model) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingType($model->meeting_type).'</a></div>';
                  },
          ],
          [
            'label'=>'Date',
              'attribute' => 'created_at',
              'format' => 'raw',
              'value' => function ($model) {
                    $chosenTime = $model->getChosenTime($model->id);
                    return '<div>'.Yii::$app->formatter->asDatetime($chosenTime->start,"MMM d").'</div>';
                  },
          ],*/
              ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view}  {decline}  {cancel}',
              'headerOptions' => ['class' => 'itemHide'],
              'contentOptions' => ['class' => 'itemHide'],
              'buttons'=>[
                  'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url,
                    [
                            'title' => Yii::t('frontend', 'view'),
                            'class' => 'icon-pad',
                    ]);
                  },
                  'decline' => function ($url, $model) {
                    return ($model->status==$model::STATUS_SENT ) ? Html::a('<span class="glyphicon glyphicon-thumbs-down"></span>', $url, [
                            'title' => Yii::t('frontend', 'decline'),
                            'class' => 'icon-pad',
                    ]) : '';
                  },
                  'cancel' => function ($url, $model) {
                    return ($model->status==$model::STATUS_SENT || $model->status==$model::STATUS_CONFIRMED ) ? Html::a('<span class="glyphicon glyphicon-remove-circle"></span>', $url, [
                            'title' => Yii::t('frontend', 'cancel'),
                            'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?'),
                            'class' => 'icon-pad',
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
        'label'=>'Details',
          'attribute' => 'meeting_type',
          'format' => 'raw',
          'value' => function ($model) {
              // to do - remove legacy code when subject didn't exist
                if ($model->subject=='' || $model->subject==$model::DEFAULT_SUBJECT) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a><br /><span class="index-participant">'.$model->getMeetingParticipants($model->id).'</span></div>';
                } else {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-participant">'.$model->getMeetingParticipants($model->id).'</span></div>';
                }
              },
      ],
      /*[
        'label'=>'Participant(s)',
          'attribute' => 'id',
          'format' => 'raw',
          'value' => function ($model) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingParticipants($model->id).'</a></div>';
              },
      ],
      [
        'label'=>'Type',
          'attribute' => 'meeting_type',
          'format' => 'raw',
          'value' => function ($model) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingType($model->meeting_type).'</a></div>';
              },
      ],
      [
        'label'=>'Created',
          'attribute' => 'created_at',
          'format' => 'raw',
          'value' => function ($model) {
                return '<div>'.Yii::$app->formatter->asDatetime($model->created_at,"MMM d").'</div>';
              },
      ],*/
          ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {trash}',
          'headerOptions' => ['class' => 'itemHide'],
          'contentOptions' => ['class' => 'itemHide'],
          'buttons'=>[
              'view' => function ($url, $model) {
                return $model->status<=$model::STATUS_SENT ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('frontend', 'view'),
                        'class' => 'icon-pad',
                ]):'' ;
              },
              'trash' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'title' => Yii::t('frontend', 'delete'),
                        'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this meeting?'),
                        'class' => 'icon-pad',
                ]);
              },
            ]
          ],
      ],
  ]);

}
?>

        <?= Html::a(Yii::t('frontend', Yii::t('frontend','Schedule a Meeting'), [
    'modelClass' => 'Meeting',
]), ['create'], ['class' => 'btn btn-success']) ?>
