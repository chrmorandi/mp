<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Meeting Planner';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?php echo Yii::t('frontend','Scheduling'); ?><br class="rwd-break" /><span class="itemHide">&nbsp;</span><?php echo Yii::t('frontend','Made Easy') ?></h1>                 

      <h3><?= Yii::t('frontend','Want to sign up now?') ?></h3>


            <p class="lead"><?php echo Yii::t('frontend','It\'s easiest to join using one of these services:'); ?></p>

  <style>
  div.container6 {
height: 10em;
display: flex;
align-items: center;
justify-content: center }
    </style>
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
<?php echo Yii::t('frontend','or ').HTML::a(Yii::t('frontend','sign up old school'),['site/signup']); ?>
<?php AuthChoice::end(); ?>
</div>





            <p><a class="btn btn-lg btn-success" href="./site/about"><?= Yii::t('frontend','Learn More') ?></a></p>

      </div> <!-- end jumbo -->


</div>
