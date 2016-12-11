<?php
  use yii\helpers\Html;
  use common\components\MiscHelpers;
  use yii\grid\GridView;
  use yii\helpers\Url;
?>
<p></p>
<?php
$pagerTemplate= ($dataProvider->count>=7 ? '{summary}{pager}':'');
// different tabs display the date in grid form separately
if ($mode =='upcoming' || $mode =='past') {
      echo GridView::widget([
          'dataProvider' => $dataProvider,
          //'filterModel' => $searchModel,
          'headerRowOptions' => ['class'=>'hidden'],
          'layout'=>'{items}'.$pagerTemplate,
          'columns' => [
          [
            'contentOptions' => ['class' => 'col-lg-11 col-xs-10'],
            'label'=>'Details',
              'attribute' => 'meeting_type',
              'format' => 'raw',
              'value' => function ($model) {
                  $chosenTime=$model->getChosenTime($model->id);
                  $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
                  // to do - remove legacy code when subject didn't exist
                    if ($model->subject=='' || $model->subject==$model::DEFAULT_SUBJECT || $model->subject==$model::DEFAULT_ACTIVITY_SUBJECT) {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader('pastplanning').'</a><br /><span class="index-time">'.$model->friendlyDateFromTimestamp($chosenTime->start,$timezone,true,true).' </span><span class="index-participant">'.$model->getMeetingParticipants().'</span></div>';
                    } else {
                      return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-time">'.$model->friendlyDateFromTimestamp($chosenTime->start,$timezone,true,true).' </span><span class="index-participant">'.$model->getMeetingParticipants().'</span></div>';
                    }
                  },
          ],
              ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view}  {download}  {decline}  {cancel}',
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
                  'download' => function ($url, $model) {
                    return ($model->status==$model::STATUS_CONFIRMED ) ? Html::a('<span class="glyphicon glyphicon-calendar"></span>', $url,
                    [
                            'title' => Yii::t('frontend', 'download for your calendar'),
                            'class' => 'icon-pad',
                    ]): '';
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
      'layout'=>'{items}'.$pagerTemplate,
      'headerRowOptions' => ['class'=>'hidden'],
      'columns' => [
      [
        'contentOptions' => ['class' => 'col-lg-11 col-xs-10'],

        'label'=>Yii::t('frontend','Subject'),
          'attribute' => 'meeting_type',
          'format' => 'raw',
          'value' => function ($model) {
              // to do - remove legacy code when subject didn't exist
                if ($model->subject=='' || $model->subject==$model::DEFAULT_SUBJECT) {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->getMeetingHeader().'</a><br /><span class="index-participant">'.$model->getMeetingParticipants().'</span></div>';
                } else {
                  return '<div><a href="'.Url::to(['meeting/view', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-participant">'.$model->getMeetingParticipants().'</span></div>';
                }
              },
      ],
          ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{view} {settings} {trash}',
          'headerOptions' => ['class' => 'itemHide'],
          'contentOptions' => ['class' => 'itemHide'],
          'buttons'=>[
              'view' => function ($url, $model) {
                return $model->status<=$model::STATUS_SENT ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('frontend', 'view'),
                        'class' => 'icon-pad',
                ]):'' ;
              },
              'settings' => function ($url, $model) {
                return $model->status<=$model::STATUS_CONFIRMED ? Html::a('<span class="glyphicon glyphicon-cog"></span>', Url::to(['meeting-setting/update', 'id' => $model->id]), [
                        'title' => Yii::t('frontend', 'settings'),
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
]), ['create'], ['class' => 'btn btn-success horizontal-pad']); ?>

<?= Html::a(Yii::t('frontend', Yii::t('frontend','Schedule an Activity'), [
'modelClass' => 'Meeting',
]), ['createactivity'], ['class' => 'btn btn-warning']); ?>
