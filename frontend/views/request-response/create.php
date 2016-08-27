<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\RequestResponse */

$this->title = Yii::t('frontend', 'Respond to Request');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting'), 'url' => ['/meeting/view','id'=>$meeting_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-response-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'responseProvider' => $responseProvider,
        'model' => $model,
        'isOrganizer'=>$isOrganizer,
        'isOwner'=>$isOwner,
        'subject'=>$subject,
    ]) ?>

</div>
