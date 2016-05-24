<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Reminder */

$this->title = Yii::t('frontend', 'Add a Reminder');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Reminders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reminder-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
