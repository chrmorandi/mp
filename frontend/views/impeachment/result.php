<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\ImpeachmentAsset;
use kartik\social\FacebookPlugin;
use kartik\social\TwitterPlugin;

ImpeachmentAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->title = Yii::t('frontend','Impeachment Estimates');
//$this->params['breadcrumbs'][] = $this->title;

?>
<div class="impeachment-results">
  <div class="row ">
    <div class="col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2 text-center">
      <h1><?= Html::encode($this->title) ?></h1>
      <p><?= Yii::t('frontend','You predict that Trump will be impeached on:')?></p>
        <h3><?= Yii::$app->formatter->asDatetime($model->estimate,'E MMM d, Y\' at \'h:mm a z') ?></h3>
        <div class="centered">
        <div class="text-center share-fb"><?= FacebookPlugin::widget(['type'=>FacebookPlugin::SHARE, 'settings' => ['size'=>'large', 'layout'=>'button_count', 'mobile_iframe'=>'false']]);// ?></div>
        <div class="share-twitter"><?= TwitterPlugin::widget(['type'=>TwitterPlugin::SHARE, 'settings' => ['size'=>'large',]]); ?></div>
      </div>
    </div>
  </div>
</div>
<div class="row ">
  <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
    <h3><?= Yii::t('frontend','Average Estimate');?></h3>
    <p><?= Yii::$app->formatter->asDatetime($avg,'E MMM d, Y\' at \'h:mm a z') ?></p>
  </div>
</div>
<div class="row ">
  <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
    <h3><?= Yii::t('frontend','Estimates by Day');?></h3>
    <p><?= Yii::t('frontend','...coming soon...')?></p>
  </div>
</div>
<div class="row ">
  <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
    <h3><?= Yii::t('frontend','Estimates by Month');?></h3>
    <p><?= $this->render('_piechart', [
        'monthyearStats' => $monthyearStats,
      ]);?>
      </p>
  </div>
</div>
