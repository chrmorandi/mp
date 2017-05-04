<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::t('frontend','About').' '.Yii::t('frontend',Yii::$app->params['site']['title']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <p class="lead"><?= Yii::t('frontend','Avoid the dreaded email chains of scheduling')?>, <?= Html::a(Yii::t('frontend',Yii::$app->params['site']['title']),Yii::$app->params['site']['url'])?> <?=Yii::t('frontend','makes planning easier whether one on one, group or social gatherings.')?>
    <p class="lead"><?= Yii::$app->params['site']['title']?> <?= Yii::t('frontend','provides the ability to collaborate with participants to suggest and choose the best time and place to meet up. It also allows participants to communicate during planning and after scheduling. Reminders are delivered with maps and contact information for participants.')?></p>
    <h2><?= Yii::t('frontend','Our History'); ?></h2>
    <p class="lead"><?= Yii::$app->params['site']['title']?> <?= Yii::t('frontend','was envisioned in 2014 on a napkin to make scheduling meetings easier. Former Microsoftee and startup veteran')?> <?= Html::a(Yii::t('frontend','Jeff Reifman'),'http://jeffreifman.com')?> <?= Yii::t('frontend','created the vision and Envato Tuts+ allowed him to write a tutorial series on')?> <?= Html::a(Yii::t('frontend','how to build startups'),'https://code.tutsplus.com/tutorials/building-your-startup-with-php-table-of-contents--cms-23316')?>
    <?= Yii::t('frontend','to help other programmers and entrepreneurs')?>.</p>
    <p class="lead"><?= Html::a(Yii::t('frontend','Follow us on Twitter'),'https://twitter.com/intent/user?screen_name=meetingio')?> <?= Yii::t('frontend','and feel free to ')?> <?= Html::a(Yii::t('frontend','contact us'),['ticket/create']) ?> <?=Yii::t('frontend','with your questions.')?></p>

</div>
