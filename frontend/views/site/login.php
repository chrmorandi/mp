<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->title = Yii::t('frontend','Login to your account');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4 col-lg-6 col-lg-offset-3 text-center">
      <h2><?= Html::encode($this->title) ?></h2>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4 text-center">
      <p><?= Yii::t('frontend','It\'s fastest using these services:'); ?></p>
      <?= yii\authclient\widgets\AuthChoice::widget([
           'baseAuthUrl' => ['site/auth','mode'=>'login'],
           'popupMode' => false,
      ]) ?>
    </div> <!-- end col-xs-6 -->
  </div> <!-- end row -->
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4">
        <p><?= Yii::t('frontend','Or, fill out the following fields to login:') ?></p>
          <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
              <?= $form->field($model, 'username')->textInput(['maxlength' => 255,'placeholder' => 'you@youremail.com'])->label(Yii::t('frontend','Email'))->hint(Yii::t('frontend','Or, use your username to log in.')) ?>
              <?= $form->field($model, 'password')->passwordInput(['placeholder' => '********'])->label(Yii::t('frontend','Password')) ?>
              <?= $form->field($model, 'rememberMe')->checkbox()->label(Yii::t('frontend','Remember me')) ?>
              <div style="color:#999;margin:1em 0">
                  <?= Html::a(Yii::t('frontend','Don\'t have a password or wish to change yours?'), ['site/request-password-reset']) ?>
              </div>
              <div class="form-group">
                  <?= Html::submitButton(Yii::t('frontend','Login'), ['class' => 'btn btn-lg btn-primary', 'name' => 'login-button']) ?>
              </div>
          <?php ActiveForm::end(); ?>
        </div> <!-- end col-lg-5 -->
    </div> <!-- end row -->
</div>
