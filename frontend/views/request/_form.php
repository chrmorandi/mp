<?php

use yii\helpers\Html;
use frontend\models\Request;
use common\components\MiscHelpers;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class ="col-xs-12 col-lg-6">
        <?php
        $options=[Request::TIME_ADJUST_NONE=>Yii::t('frontend','no, keep the time'),
          Request::TIME_ADJUST_ABIT=>Yii::t('frontend','yes, adjust the time a bit')];
          if ($countTimes>1) {
            $options[Request::TIME_ADJUST_OTHER]=Yii::t('frontend','yes, pick another time');
          }
        echo $form->field($model, 'time_adjustment')->label(Yii::t('frontend','Do you want to adjust the time?'))
          ->dropDownList(
              $options,           // Flat array ('id'=>'label')
              ['options' => [Request::TIME_ADJUST_ABIT => ['Selected'=>'selected']],
              'id'=>'adjust_how']
            );
          ?>
    <div id="choose_earlier" class="">
    <?php
    echo $form->field($model, 'alternate_time')->label(Yii::t('frontend','Choose a time slightly earlier or later than {currentStartStr}',['currentStartStr'=>$currentStartStr]))
      ->dropDownList(
          $altTimesList,
          ['options' => [$currentStart => ['disabled' => true]]]
                     // Flat array ('id'=>'label')
          //,['prompt'=>'select an alternate time']
              // options

      );
      ?>
    </div>
    <div id="choose_another" class="hidden">
    <?php
    echo $form->field($model, 'meeting_time_id')->label(Yii::t('frontend','Pick one of the other times'))
      ->dropDownList(
          $times
      );
      ?>
    </div>
    <?php
    if ($countPlaces>1) {
    echo $form->field($model, 'meeting_place_id')->label(Yii::t('frontend','Do you want to request a different place?'))
      ->dropDownList(
          $places
        //  ,['prompt'=>'select an alternate place']
      )->hint(Yii::t('frontend','to select alternate places, just click the dropdown'));
    }
      ?>
    <?= $form->field($model, 'note')->textarea(['rows' => 6])->hint(Yii::t('frontend','Optional'))->label(Yii::t('frontend','Add a message to your request here')); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view','id'=>$model->meeting_id], ['class' => 'btn btn-danger']) ?>
    </div>
  </div> <!-- end col1 -->
  </div> <!-- end row -->
    <?php ActiveForm::end();
    $this->registerJsFile(MiscHelpers::buildUrl().'/js/request.js',['depends' => [\yii\web\JqueryAsset::className()]]);
    ?>
</div>
