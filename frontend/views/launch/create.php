<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Launch */

$this->title = Yii::t('frontend', 'Create Launch');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Launches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="launch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
