<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = Yii::t('frontend','Sorry, page not found');
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode(Yii::t('frontend','We\'re not sure what you are looking for.'))) ?>
        <?= Html::a(Yii::t('frontend','Please contact our support team'),['/ticket/create']); ?>.
    </div>
</div>
