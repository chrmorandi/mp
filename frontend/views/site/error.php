<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Sorry, We Encountered a Problem';
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert alert-danger">
        <?= nl2br(Html::encode(Yii::t('frontend','An error occurred while the Web server was processing your request.'))) ?>
        Please <?= Html::a(Yii::t('frontend','Contact our support team'),['/ticket/create']); ?> and tell us what you were trying to do. Thank you.
    </div>
</div>
