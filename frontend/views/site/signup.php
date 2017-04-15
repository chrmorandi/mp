<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
$this->title = 'Signup';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4">
      <h2><?= Yii::t('frontend','Signup') ?></h2>
      <p><?= Yii::t('frontend','It\'s easiest to join using one of these services:'); ?></p>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-4">
        <?= yii\authclient\widgets\AuthChoice::widget([
             'baseAuthUrl' => ['site/auth','mode'=>'signup'],
             'popupMode' => false,
        ]) ?>
      </div> <!-- end col-lg-5 -->
    </div> <!-- end row -->
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-4">
          <p><?=Yii::t('frontend','Or, register with your email:');?></p>

            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username')->textInput(['placeholder' => 'JaneSmith'])->label(Yii::t('frontend','Username')) ?>
                <?=
                $form->field($model, 'email', ['errorOptions' => ['class' => 'help-block' ,'encode' => false]])->label(Yii::t('frontend','Email'))->textInput(['placeholder' => 'you@youremail.com']) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => '********'])->label(Yii::t('frontend','Password')) ?>
                <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::className(), [
                      // configure additional widget properties here
                  ])->label(Yii::t('frontend','Captcha')) ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('frontend','Signup now'), ['class' => 'btn btn-lg btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
