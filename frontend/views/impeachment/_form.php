<?php
use yii\jui\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Impeachment */
/* @var $form ActiveForm */
?>
<div class="row">
  <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
    <div class="impeachment-form">
      <h4><?= Yii::t('frontend','Choose the date and time'); ?></h4>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'estimate',['options'=>['id'=>'datepicker']])->label('')->widget(\yii\jui\DatePicker::classname(), [
            //'language' => 'ru',
            'inline'=>true,
            //'dateFormat' => 'yyyy-MM-dd',
            'clientOptions'=>['changeYear'=>true,'changeMonth'=>true,'maxDate'=>'+8y -110d','minDate'=>1],
        ]) ?>

        <select class="combobox input-large form-control impeach-width" id="impeachment-hour" name="Impeachment[hour]">
        <?php
        $cnt=5;
        while ($cnt <24) {
          ?>
          <option value="<?= $cnt ?>" <?= ($cnt==20?'selected':'')?>><?= $hoursArray[$cnt];?></option>
          <?php
          $cnt+=1;
        }
        ?>
        </select>
        <p></p>
          <div class="form-group">
              <?= Html::submitButton(Yii::t('frontend', 'Submit Your Choice'), ['class' => 'btn btn-primary btn-lg impeach-width']) ?>
          </div>
          <div class="hint-block"><?= Yii::t('frontend','If a friend referred you, they may be able to see your guess, but otherwise it will be displayed anonymously.')?></div>
        <?php ActiveForm::end(); ?>

    </div><!-- impeachment-form -->
  </div> <!-- end col-lg-5 -->
</div> <!-- end row -->
