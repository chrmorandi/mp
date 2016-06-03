<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = Yii::t('frontend','Schedule a Meeting');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">

    <div class="row">
        <div class="col-lg-5">
          <p><?php echo Yii::t('frontend','Or, sign up with your existing account at one of these services:'); ?></p>
          <?= yii\authclient\widgets\AuthChoice::widget([
               'baseAuthUrl' => ['site/auth','mode'=>'login'],
               'popupMode' => false,
          ]) ?>
        </div> <!-- end col-lg-5 -->
      </div> <!-- end row -->
      <div class="row">
        <!-- graphic slidehow or video of scheduling -->
      </div>
</div>
