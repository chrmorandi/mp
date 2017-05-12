<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\ImpeachmentAsset;
ImpeachmentAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->title = Yii::t('frontend','When Do You Think Trump Will Be Impeached?');
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="impeachment-index">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2">
      <h2 style="font-size:185%;"><?= Html::encode($this->title) ?></h2>
      <p class="lead"><?= Yii::t('frontend','We\'re asking people and charting everyone\'s guesses.')?> <?= Yii::t('frontend','When Congress finally acts, we\'ll help you plan your celebration.');?></p>
    </div>
  </div>
<?php
if (Yii::$app->user->isGuest) {
?>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
      <p><strong><?= Yii::t('frontend','1) Sign up using one of these services:'); ?></strong></p><p class="normal"><?= Yii::t('frontend','It will help us keep the trolls out and allows you to schedule future social and business events using our collaborative planning service.') ?></p>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">

      <?= yii\authclient\widgets\AuthChoice::widget([
           'baseAuthUrl' => ['site/auth','mode'=>'login'],
           'popupMode' => false,
      ]) ?>
    </div> <!-- end col-xs-6 -->
  </div> <!-- end row -->
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">

        <?= Html::img(Yii::$app->params['site']['url'].'/img/impeachment.gif', ['id'=>'impeachment-image']); ?>

    </div>
  </div>
<?php
} else {
?>
<?= $this->render('_timezone_alerts'); ?>
        <?= $this->render('_form', [
            'model' => $model,
            'hoursArray'=>$hoursArray,
        ]);
} ?>
<?= Html::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= Html::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
</div>
