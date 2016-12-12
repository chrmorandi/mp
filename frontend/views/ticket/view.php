<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\i18n\Formatter;
use common\components\MiscHelpers;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Ticket */

$this->title = $model->subject;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Tickets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::encode($model->details) ?></p>
    <hr />
    <div class="ticket-reply-list">
      <?php foreach ($model->ticketReplies as $r) { ?>
        <div class="ticket-reply">
          <p><?= $r->reply; ?><br />
            <em><?= Yii::t('frontend','Posted by {username}',['username'=>MiscHelpers::getDisplayName($r->posted_by)]); ?> <?= Yii::$app->formatter->asRelativeTime($r->created_at); ?></em>
            <p>
            <hr />
          </div>
      <?php } ?>
      </div>
    <div class="ticket-reply-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($reply, 'reply')->label(Yii::t('frontend','Reply to this ticket'))->textarea(['rows' => 6]) ?>
        <div class="form-group">
            <?= Html::submitButton($reply->isNewRecord ? Yii::t('frontend', 'Reply') : Yii::t('frontend', 'Reply'), ['class' => $reply->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
              <?= Html::a(Yii::t('frontend', 'Close ticket'), ['close', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
        </div>
        <?php ActiveForm::end(); ?>

    </div>

</div>
