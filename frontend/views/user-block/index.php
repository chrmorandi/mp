<?php


use yii\helpers\Html;
use common\components\MiscHelpers;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\UserBlockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'People You Have Blocked');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-block-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
              'label'=>'Blocked People',
                'attribute' => 'blocked_user_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div>'.MiscHelpers::getDisplayName($model->blocked_user_id).'</div>';
                    },
            ],
            ['class' => 'yii\grid\ActionColumn','header'=>'Options','template'=>'{cancel}',
            'buttons'=>[
                'cancel' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, [
                          'title' => Yii::t('frontend', 'Unblock'),
                          //'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
                  ]);
                }

              ]
            ],        ],
    ]); ?>
<?php Pjax::end(); ?></div>
