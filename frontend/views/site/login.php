<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title).' '.Yii::t('frontend','to Meeting Planner') ?></h1>
    <div class="row">
        <div class="col-lg-5">
          <p>It's easiest to login using one of the following services:</p>
          <?= yii\authclient\widgets\AuthChoice::widget([
               'baseAuthUrl' => ['site/auth','mode'=>'login'],
               'popupMode' => false,
          ]) ?>
        </div> <!-- end col-lg-5 -->
      </div> <!-- end row -->

    <div class="row">
        <div class="col-lg-5">
          <p>Or, fill out the following fields to login:</p>
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => 255])->label(Yii::t('frontend','Username'))->hint(Yii::t('frontend','You can also use your email address when logging in.')) ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
                <div style="color:#999;margin:1em 0">
                    If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div> <!-- end col-lg-5 -->
    </div> <!-- end row -->

</div>
