<?php
  use yii\helpers\Html;
  use yii\grid\GridView;
  use yii\helpers\Url;
?>
<p></p>
<?php
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
                    if ($model->subject=='') {
                      //$model->getMeetingHeader()
                      return '<div><a href="'.Url::to(['template/update', 'id' => $model->id]).'">'.''.'</a><br /><span class="index-participant">'.''.'</span></div>';
                    } else {
                      return '<div><a href="'.Url::to(['template/update', 'id' => $model->id]).'">'.$model->subject.'</a><br /><span class="index-participant">'.''.'</span></div>';
                    }
                  },
          ],
          ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{update}  {trash}',
              'headerOptions' => ['class' => 'itemHide'],
              'contentOptions' => ['class' => 'itemHide'],
              'buttons'=>[
                  'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,
                    [
                            'title' => Yii::t('frontend', 'update'),
                            'class' => 'icon-pad',
                    ]);
                  },
                  'trash' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                            'title' => Yii::t('frontend', 'delete'),
                            'data-confirm' => Yii::t('frontend', 'Are you sure you want to delete this template?'),
                            'class' => 'icon-pad admin-pad',
                    ]);
                  },
                ]
              ],
          ],
      ]);

  echo Html::a(Yii::t('frontend', 'Create a Meeting Template', [
    'modelClass' => 'Template',
]), ['create'], ['class' => 'btn btn-success'])
?>
