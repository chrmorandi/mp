<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MessageLog */

$this->title = Yii::t('backend', 'Create Message Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Message Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
