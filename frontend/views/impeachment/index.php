<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\ImpeachmentAsset;
ImpeachmentAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->title = Yii::t('frontend','Schedule Your Impeachment Party');
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-login">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2">
      <h2><?= Html::encode($this->title) ?></h2>
    </div>
  </div>
<?php
if (Yii::$app->user->isGuest) {
?>
  <div class="row">
    <div class="col-xs-10 col-xs-offset-2 col-md-4 col-md-offset-4">
      <p><strong><?= Yii::t('frontend','1) Prove you\'re not a troll'); ?></strong></p>
      <?= yii\authclient\widgets\AuthChoice::widget([
           'baseAuthUrl' => ['site/auth','mode'=>'login'],
           'popupMode' => false,
      ]) ?>
    </div> <!-- end col-xs-6 -->
  </div> <!-- end row -->
  <div class="row">
    <div class="col-xs-8 col-xs-offset-2 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
      <center>
        <?= Html::img('http://localhost:8888/mp/img/impeachment.gif', ['id'=>'impeachment-image']); // https://meetingplanner.io?>
      </center>
    </div>
  </div>
<?php
} else {
?>
        <?= $this->render('_form', [
            'model' => $model,
            'hoursArray'=>$hoursArray,
        ]);
} ?>
</div>
