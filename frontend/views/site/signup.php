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
      <h2><?= Html::encode($this->title).' '.Yii::t('frontend','with Meeting Planner') ?></h2>
      <p><?php echo Yii::t('frontend','It\'s easiest to join using one of these services:'); ?></p>
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
          <p><?=Yii::t('frontend','Or, fill out the following fields to register manually:');?></p>

            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username')->textInput(['placeholder' => 'JaneSmith']) ?>
                <?=
                $form->field($model, 'email', ['errorOptions' => ['class' => 'help-block' ,'encode' => false]])->textInput(['placeholder' => 'you@youremail.com']) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => '********']) ?>
                <?= $form->field($model, 'captcha')->widget(\yii\captcha\Captcha::classname(), [
                      // configure additional widget properties here
                  ]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Signup Now', ['class' => 'btn btn-lg btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>
