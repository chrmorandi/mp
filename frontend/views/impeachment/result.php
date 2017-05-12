<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\ImpeachmentAsset;
ImpeachmentAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->title = Yii::t('frontend','Impeachment Party Scheduling Results');
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="impeachment-results">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2">
      <h2><?= Html::encode($this->title) ?></h2>
    </div>
  </div>
</div>
