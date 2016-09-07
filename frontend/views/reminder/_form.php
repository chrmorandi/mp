<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Reminder;

/* @var $this yii\web\View */
/* @var $model frontend\models\Reminder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reminder-form">
  <div class="row">
  <div class="col-md-6">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-md-12">
    <?php
    $durationList = [1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,8=>8,10=>10,12=>12,16=>16,20=>20,24=>24,36=>36,48=>48,72=>72];
    echo $form->field($model, 'duration_friendly')->label(Yii::t('frontend','Time before meeting'))
      ->dropDownList(
          $durationList,           // Flat array ('id'=>'label')
          ['prompt'=>'select a timespan']    // options
      );
      ?>
</div>
</div>
<div class="row">
<div class="col-md-12">
      <?php
      $unitList = [Reminder::UNIT_MINUTES=>'minute(s)',Reminder::UNIT_HOURS=>'hour(s)',Reminder::UNIT_DAYS=>'day(s)'];
      echo $form->field($model, 'unit')->label(Yii::t('frontend','Timespan e.g. minutes, hours, days'))
        ->dropDownList(
            $unitList,           // Flat array ('id'=>'label')
            ['prompt'=>'select a timespan unit']    // options
        );
        ?>
</div>
</div>
<div class="row">
  <div class="col-md-12">
        <?php
        $typeList = [Reminder::TYPE_EMAIL=>'email',Reminder::TYPE_SMS=>'text (not yet available)',Reminder::TYPE_BOTH=>'email and text (not available)'];
        echo $form->field($model, 'reminder_type')->label('Delivery via')
          ->dropDownList(
              $typeList,           // Flat array ('id'=>'label')
              ['prompt'=>'select delivery mode']    // options
          );
          ?>
  </div>
</div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit New Reminder') : Yii::t('frontend', 'Update Reminder'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this reminder?'),
                //'method' => 'post',
            ],
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
  </div> <!-- end col8 -->
</div>
</div>
