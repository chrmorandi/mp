<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserProfile */

$this->title = Yii::t('frontend', 'Update {modelClass}', [
    'modelClass' => 'User Profile',
]) ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('frontend', 'Update');
?>
<div class="user-profile-update">

    <h1><?= Yii::t('frontend', 'Update Your Profile') ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
