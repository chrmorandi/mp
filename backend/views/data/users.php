<?php
/* @var $this yii\web\View */
use yii\grid\GridView;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\UserData;


$this->title = Yii::t('backend','Meeting Planner');
?>
<div class="site-index">
  <div class="body-content">
        <h1>User Email Domain Distribution</h1>

        <h3>Number of Meetings Created By Organizers</h3>
        <?= GridView::widget([
          'dataProvider' => $data->domains,
          'columns' => [
            'domain',
            'cnt',
          ],
        ]); ?>

        <h1>User Email Domain Extension Distribution</h1>
        <?= GridView::widget([
          'dataProvider' => $data->domain_exts,
          'columns' => [
            'domain_ext',
            'cnt',
          ],
        ]); ?></div>
