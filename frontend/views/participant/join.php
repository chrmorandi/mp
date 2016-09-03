<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */

$this->title = Yii::t('frontend', 'Join the Meeting');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="participant-create">
    <h1><?=Yii::t('frontend','Welcome, want to join the meeting?') ?></h1>
    <?= $this->render('_join_form', [
        'model' => $model,
    ]) ?>
</div>
