<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;
/* @var $this yii\web\View */
/* @var $model frontend\models\UserContact */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-contact-form">

  <?php $form = ActiveForm::begin([
    'id'=> 'user-contact-form',
  ]); ?>

    <?= $form->field($model, 'contact_type')
            ->dropDownList(
                $model->getUserContactTypeOptions(),
	                ['prompt'=>Yii::t('frontend','What type of contact is this?'),
                  'id'=> 'user-contact-type',]
	            )->label(Yii::t('frontend','Type of Contact')) ?>

    <?= $form->field($model, 'info')->textInput(['maxlength' => 255])->label(Yii::t('frontend','Contact Information'))->hint(Yii::t('frontend','e.g. phone number, skype address, et al.')) ?>
    <?php
      if ($model->contact_type == $model::TYPE_PHONE) {
        $visibility = true;
      } else {
        $model->accept_sms = $model::SETTING_NO;
        $visibility = false;
      }
      ?>
        <span class="setting-label <?php if (!$visibility) { echo 'hidden';} ?>">
        <?= $form->field($model, 'accept_sms')->checkbox(['label' => Yii::t('frontend','Receive texts at this number?'), 'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
        </span>
    <?= $form->field($model, 'details')->textarea(['rows' => 6])->hint(Yii::t('frontend','Specify any additional details the person may need to reach you with this information.')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end();
     $this->registerJsFile(MiscHelpers::buildUrl().'/js/user_contact.js',['depends' => [\yii\web\JqueryAsset::className()]]);
     ?>

</div>
