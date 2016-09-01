<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */

$this->title = Yii::t('frontend', '', [
    'modelClass' => 'Meeting',
]) . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="meeting-update">

    <h1><?= Yii::t('frontend','Update Meeting') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'subjects' =>  $model->defaultSubjectList(),
    ]) ?>

</div>
