<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'time_adjustment')->textInput() ?>

    <?= $form->field($model, 'number_seconds')->textInput() ?>

    <?= $form->field($model, 'meeting_time_id')->textInput() ?>



    <?php

    echo $form->field($model, 'meeting_place_id')->label(Yii::t('frontend','Pick a different place'))
      ->dropDownList(
          $places,
          ['prompt'=>'select an alternate place']
      );
      ?>
    <?= $form->field($model, 'note')->textarea(['rows' => 6])->hint(Yii::t('frontend','Optional'))->label(Yii::t('frontend','Add a message to your response here')); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view','id'=>$model->meeting_id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
