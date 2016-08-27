<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\components\MiscHelpers;
/* @var $this yii\web\View */
/* @var $model frontend\models\RequestResponse */
/* @var $form yii\widgets\ActiveForm */
?>

<p><em>
<?= $subject ?>
</em>
</p>

<?= GridView::widget([
    'dataProvider' => $responseProvider,
    'columns' => [
      [
      'label'=>'Responses from Other Participants',
        'attribute' => 'responder_id',
        'format' => 'raw',
        'value' => function ($model) {
                $note='';
                if (!empty($model->note)) {
                  $note = ' said, "'.$model->note.'"';
                }
                return '<div>'.MiscHelpers::getDisplayName($model->responder_id).' '.$model->lookupOpinion().$note.'</div>';                
            },
    ],
  ],
]); ?>
<div class="request-response-form">
    <?php $form = ActiveForm::begin(); ?>
      <?= BaseHtml::activeHiddenInput($model, 'responder_id'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'request_id'); ?>
    <?= $form->field($model, 'note')->label(Yii::t('frontend','Include a note'))->textarea(['rows' => 6])->hint(Yii::t('frontend','optional')) ?>
<?php
  if (!$isOwner && $isOrganizer) {
?>
  <p><em><?= Yii::t('frontend','Since you are an organizer, you can accept the request and make the changes or reject it.');?></em></p>
<?php
  }
?>
<?php
  if ($isOrganizer) {
?>
<div class="form-group">
  <?= Html::submitButton(Yii::t('frontend', 'Accept and Make Changes'), ['class' => 'btn btn-success','name'=>'accept',]) ?>
  <?= Html::submitButton(Yii::t('frontend', 'Decline Request'),['class' => 'btn btn-danger','name'=>'reject',
      'data' => [
          'confirm' => Yii::t('frontend', 'Are you sure you want to decline this request?'),
          'method' => 'post',
      ],]) ?>
</div>
<?php
  }
?>
<?php
  if (!$isOwner && $isOrganizer) {
?>
  <p><em><?= Yii::t('frontend','Or, you can just express your opinion and defer to other organizers.');?></em></p>
<?php
  }
?>
<?php
  if (!$isOwner) {
?>
<?php
  if (!$isOrganizer) {
?>
<p><em><?= Yii::t('frontend','Please share your opinion of this request for the organizers to consider.');?></em></p>
<?php
 }
 ?>
<div class="form-group">
  <?= Html::submitButton(Yii::t('frontend', 'Like'), ['class' => 'btn btn-success','name'=>'like',]) ?>
  <?= Html::submitButton(Yii::t('frontend', 'Don\'t Care'), ['class' => 'btn btn-info','name'=>'neutral',]) ?>
  <?= Html::submitButton(Yii::t('frontend', 'Dislike'),['class' => 'btn btn-danger','name'=>'dislike',]) ?>

</div>
<?php
  }
?>
    <?php ActiveForm::end(); ?>

</div>
