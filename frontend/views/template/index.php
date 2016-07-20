<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meeting Templates');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->title ?></h1>

    <div class="template-index">
      <?php
      ?>
      <?= $this->render('_grid', [
          'dataProvider' => $dataProvider,
      ]) ?>
    </div> <!-- end of planning meetings tab -->
