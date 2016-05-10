<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Reminder;

/* @var $this yii\web\View */
/* @var $model frontend\models\Reminder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-form">
  <div class="col-md-8">
    <?php $form = ActiveForm::begin(); ?>


    <div class="col-md-4">
    <?php
    $durationList = [1,2,3,4,5,6,8,10,12,16,20,24,36,48,72];
    //$durationList = [1=>'1 hour',2=>'2 hours',3=>'3 hours',4=>'4 hours',5=>'5 hours',6=>'6 hours',12=>'12 hours',24=>'24 hours',48=>'48 hours',72=>'72 hours'];
    echo $form->field($model, 'duration_friendly')->label(Yii::t('frontend','Time before meeting'))
      ->dropDownList(
          $durationList,           // Flat array ('id'=>'label')
          ['prompt'=>'select a timespan']    // options
      );
      ?>
</div>
<div class="col-md-4">
      <?php
      $unitList = [Reminder::UNIT_MINUTES=>'minute(s)',Reminder::UNIT_HOURS=>'hour(s)',Reminder::UNIT_DAYS=>'day(s)'];
      echo $form->field($model, 'unit')->label('')
        ->dropDownList(
            $unitList,           // Flat array ('id'=>'label')
            ['prompt'=>'select a timespan unit']    // options
        );
        ?>
</div>
</div>
  <div class="col-md-8">
        <?php
        $typeList = [Reminder::TYPE_EMAIL=>'email',Reminder::TYPE_SMS=>'text (not yet available)',Reminder::TYPE_BOTH=>'email and text (not available)'];
        echo $form->field($model, 'reminder_type')->label('Delivery via')
          ->dropDownList(
              $typeList,           // Flat array ('id'=>'label')
              ['prompt'=>'select delivery mode']    // options
          );
          ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Create') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div> <!-- end col8 -->
</div>
