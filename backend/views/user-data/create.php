<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserData */

$this->title = Yii::t('backend', 'Create User Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'User Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
