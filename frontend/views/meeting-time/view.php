<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\models\Meeting;
use common\components\MiscHelpers;


/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingTime */

$this->title = Meeting::friendlyDateFromTimestamp($model->start,$timezone);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$model->meeting_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-time-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
          ['label' => Yii::t('frontend','Suggested by'),
            'value' => Html::encode(MiscHelpers::getDisplayName($model->suggested_by)),
            'format' => 'raw'
          ],
          ],
        ])
     ?>

    <p>
        <?= Html::a(Yii::t('frontend', 'Return to Meeting'), ['/meeting/view', 'id' => $model->meeting_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Remove this Time Option'), ['remove', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want delete this meeting time option?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
