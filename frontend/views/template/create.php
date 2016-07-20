<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\Template */

$this->title = Yii::t('frontend', 'Create Template');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Templates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="template-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'subjects' => $subjects,
    ]) ?>

</div>
