<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\HomeAsset;
HomeAsset::register($this);

//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::$app->params['site']['title'];
?>
<div class="site-index ">
    <div class="jumbotron jumbo-novert">
        <h1><?php echo Yii::t('frontend','Simple Scheduling'); ?></h1>

            <p class="lead"><?php echo Yii::t('frontend','sign up using one of these services'); ?></p>

<div class="container6">
  <?php $authAuthChoice = AuthChoice::begin([
    'baseAuthUrl' => ['site/auth','mode'=>'signup'],
    'popupMode' => false,
]); ?>

<ul class="auth-clients clear" style ="">
<?php foreach ($authAuthChoice->getClients() as $client): ?>
    <li class="auth-client"><?php $authAuthChoice->clientLink($client) ?></li>
<?php endforeach; ?>
</ul>
<?php echo Yii::t('frontend','or ').HTML::a(Yii::t('frontend','use your email'),['site/signup']); ?>
<?php AuthChoice::end(); ?>
</div>
  </div> <!-- end jumbo -->
<?= $this->render('launch');?>
</div>
<hr />
<?= $this->render('_video_carousel.php',['urlPrefix'=>$urlPrefix]);?>

<div class="centered">
<p><a class="btn btn-lg btn-success" href="features"><?= Yii::t('frontend','Learn more') ?></a></p>
</div>
