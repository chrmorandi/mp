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
          ],
          // to do: make this conditional for tabs, show delete on cancel tab, no cancel on past tab
              ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {cancel}',
              'buttons'=>[
                  'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('frontend', 'view'),
                    ]);
                  },
                  'cancel' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                            'title' => Yii::t('frontend', 'cancel'),
                            'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
                    ]);
                  }

                ]
              ],
          ],
      ]);
} else {
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
      ],
      // to do: make this conditional for tabs, show delete on cancel tab, no cancel on past tab
          ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {cancel}',
          'buttons'=>[
              'view' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('frontend', 'view'),
                ]);
              },
              'cancel' => function ($url, $model) {
                return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                        'title' => Yii::t('frontend', 'cancel'),
                        'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
                ]);
              }

            ]
          ],
      ],
  ]);

}
?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Create {modelClass}', [
    'modelClass' => 'Meeting',
]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
