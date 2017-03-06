<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\typeahead\TypeaheadBasic;
use common\components\MiscHelpers;
/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="meeting-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php /* echo $form->field($model, 'meeting_type')
            ->dropDownList(
                $model->getMeetingTypeOptions(),
                ['prompt'=>Yii::t('frontend','What type of meeting is this?')]
            )->label(Yii::t('frontend','Meeting Type'))
            */
            ?>
        <div class="row">
          <div class="col-xs-12 col-md-8 col-lg-6">
    <?php
    echo $form->field($model, 'subject')->widget(TypeaheadBasic::className(), [
    'data' => $subjects,
    'options' => ['placeholder' => Yii::t('frontend','what\'s the subject of this meeting?'),
    'id'=>'meeting-subject',
      //'class'=>'input-large form-control'
    ],
    'pluginOptions' => ['highlight'=>true],
]);
?>
      </div>
      <div class="col-md-4 col-lg-6">
      </div>

      <div class="col-xs-12 col-md-8 col-lg-6">
    <?php // $form->field($model, 'subject')->textInput(['maxlength' => 255])->label(Yii::t('frontend','Subject')) ?>
    <div class="itemHide">
    <?= $form->field($model, 'message')->textarea(['rows' => 6,'id'=>'meeting-message'])->label(Yii::t('frontend','Additional information'))->hint(Yii::t('frontend','Optional')); ?>
    </div>
  </div>
</div>

    <div class="form-group panel-what-buttons">
      <span class="button-pad">
        <?= Html::a(Yii::t('frontend','Update'), 'javascript:void(0);', ['class' => 'btn btn-primary','onclick'=>'updateWhat('.$model->id.');']) ?>
      </span><span class="button-pad">

        <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelWhat();']) // ['/meeting/view', 'id' => $model->id] ?>
      </span>
    </div>

    <?php ActiveForm::end();
     $this->registerJsFile(MiscHelpers::buildUrl().'/js/meeting_subject.js',['depends' => [\yii\web\JqueryAsset::className()]]);
     ?>
</div>
