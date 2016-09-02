<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */

$this->title = Yii::t('frontend', 'Join the Meeting');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Home'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participant-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_join_form', [
        'model' => $model,
    ]) ?>
</div>
