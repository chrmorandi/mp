<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\models\Meeting;
use common\components\MiscHelpers;


/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingActivity */

$this->title = Html::decode($title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-activity-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $activity,
        'attributes' => [
          ['label' => Yii::t('frontend','Suggested by'),
            'value' => Html::encode(MiscHelpers::getDisplayName($activity->suggested_by)),
            'format' => 'raw'
          ],
          ],
        ])
     ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Return to Meeting'), ['/meeting/view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Remove this Activity Option'), ['/meeting-activity/remove', 'meeting_id'=>$model->id,'activity_id' => $activity->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want delete this meeting activity option?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
