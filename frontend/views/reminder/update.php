<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Reminder */

$this->title = Yii::t('frontend', 'Update {modelClass}', [
    'modelClass' => 'Reminder',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="reminder-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
