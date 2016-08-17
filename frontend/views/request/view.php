<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\models\Meeting;

/* @var $this yii\web\View */
/* @var $model frontend\models\Request */

$this->title = Yii::t('frontend','Request from ').$requestor;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Requests'), 'url' => ['index','meeting_id'=>$model->meeting_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::encode($content) ?>
      <?php if ($model->note<>'') { ?>
        <p><em><?= Html::encode($model->note) ?></em></p>
      <?php } ?>
    <p>
      <?php
        if ($model->requestor_id != Yii::$app->user->getId() && ($model->meeting->viewer == Meeting::VIEWER_ORGANIZER || $meetingSettings->participant_reopen)) {
          ?>
          <?= Html::a(Yii::t('frontend', 'Accept'), ['accept', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
          <?= Html::a(Yii::t('frontend', 'Reject'), ['reject', 'id' => $model->id], [
              'class' => 'btn btn-danger',
              'data' => [
                  'confirm' => Yii::t('frontend', 'Are you sure you want to reject this request?'),
                  'method' => 'post',
              ],
          ]) ?>
      <?php
    } else {
      ?>
      <p><em>
      <?=Yii::t('frontend','Waiting for organizer(s) to respond.')?>
    </em></p>
      <?= Html::a(Yii::t('frontend', 'Withdraw Your Request'), ['withdraw', 'id' => $model->id], [
          'class' => 'btn btn-danger',
          'data' => [
              'confirm' => Yii::t('frontend', 'Are you sure you want to withdraw your request?'),
              'method' => 'post',
          ],
      ]) ?>
    </p>
      <?php
      }
       ?>


    </p>

</div>
