<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title).' '.Yii::t('frontend','with Meeting Planner') ?></h1>

    <div class="row">
        <div class="col-lg-5">
          <p><?php echo Yii::t('frontend','It\'s easiest to join using one of these services:'); ?></p>
          <?= yii\authclient\widgets\AuthChoice::widget([
               'baseAuthUrl' => ['site/auth','mode'=>'signup'],
               'popupMode' => false,
          ]) ?>
        </div> <!-- end col-lg-5 -->
      </div> <!-- end row -->

    <div class="row">
      <p>Or, fill out the following fields to register manually:</p>
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username') ?>
                <?=
                $form->field($model, 'email', ['errorOptions' => ['class' => 'help-block' ,'encode' => false]])->textInput() ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [
                      // configure additional widget properties here
                  ]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
