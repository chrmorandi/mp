<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserContact */

$this->title = Yii::t('frontend', 'Verify {modelClass}', [
    'modelClass' => 'Contact',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'User Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Verify ').$model->info];

?>
<div class="user-contact-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
      <div class="col-lg-6 col-md-6 col-xs-12">
        <div class="user-contact-form">

          <?php $form = ActiveForm::begin([
            'id'=> 'user-contact-verify-form',
          ]); ?>
            <?= $form->field($model, 'verify')
                  ->textInput(['maxlength' => 4])
                  ->label(Yii::t('frontend','Please enter the verification code')) ?>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('frontend', 'Submit'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end();
             ?>
        </div>
      </div>
    </div>

</div>
