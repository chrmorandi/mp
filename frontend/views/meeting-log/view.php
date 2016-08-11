<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting History'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="meeting-log-view">

    <h1><?php echo  Html::encode($this->title) ?></h1>

    <p>
        <?php echo  Html::a(Yii::t('frontend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo  Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo  DetailView::widget([
        'model' => $model,
        'attributes' => [
            'action',
            'actor_id',
            'item_id',
            'extra_id',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
