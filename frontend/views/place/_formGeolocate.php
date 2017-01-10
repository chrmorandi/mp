<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;

use frontend\assets\LocateAsset;
LocateAsset::register($this);

/* @var $this yii\web\View */
/* @var $model frontend\models\Place */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="place-form">
  <?php $form = ActiveForm::begin(); ?>
  <div class="col-md-6 col-lg-4">
  <div id="preSearch" class="center">
  <?php echo Html::a('Lookup Location', ['lookup'], ['class' => 'btn btn-success', 'onclick' => "javascript:beginSearch();return false;"]) ?>

  </div>

    <div id="searchArea" class="hidden">
      <div id="autolocateAlert">
      </div> <!-- end autolocateAlert -->
      <p>Searching for your current location...<span id="status"></span></p>
      <article>
      </article>
      <div class="form-actions hidden" id="actionBar">
    	</div> <!-- end action Bar-->
    </div>   <!-- end searchArea -->
    <p><br /></p>
    </div> <!-- end col 2 -->
<div class="col-md-6">

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'website')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'place_type')
            ->dropDownList(
                $model->getPlaceTypeOptions(),
                ['prompt'=>'What type of place is this?']
            )->label('Type of Place') ?>

    <?= $form->field($model, 'notes')->textarea() ?>

    <?= BaseHtml::activeHiddenInput($model, 'lat'); ?>
    <?= BaseHtml::activeHiddenInput($model, 'lng'); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Submit' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend','Cancel'), ['/place'], ['class' => 'btn btn-danger']) ?>
    </div>

</div> <!-- end col 1 -->
      <?php ActiveForm::end(); ?>
  </div>
