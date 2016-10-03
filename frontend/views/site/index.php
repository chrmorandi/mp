<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\HomeAsset;
HomeAsset::register($this);

//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Meeting Planner';
?>
<div class="site-index">
    <div class="jumbotron jumbo-novert">
        <h1><?php echo Yii::t('frontend','Scheduling'); ?><br class="rwd-break" /><span class="itemHide">&nbsp;</span><?php echo Yii::t('frontend','Made Easy') ?></h1>

      <h3><?= Yii::t('frontend','Want to sign up now?') ?></h3>

            <p class="lead"><?php echo Yii::t('frontend','It\'s easiest to join using one of these services:'); ?></p>

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
  </div> <!-- end jumbo -->
<hr />
<?= $this->render('launch');?>
<hr />
</div>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
    <li data-target="#myCarousel" data-slide-to="3"></li>
    <li data-target="#myCarousel" data-slide-to="4"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item active">
      <img src="<?= $urlPrefix ?>/img/carousel-1.jpg" alt="Chania">
      <div class="carousel-caption">
        <h3>Adding Participants</h3>
        <p>Add one or many people to a meeting.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-2.jpg" alt="Chania">
      <div class="carousel-caption">
        <h3>Choosing Times and Places</h3>
        <p>Adding dates, times and places is fast and easy.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-3.jpg" alt="Chania">
      <div class="carousel-caption">
        <h3>Choose the Date, Time and Place Together</h3>
        <p>Organizers (or participants) can choose the time and place.</p>
      </div>
    </div>

    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-4.jpg" alt="Chania">
      <div class="carousel-caption">
        <h3>Add to Your Calendar</h3>
        <p>Everyone will receive a calendar entry via email or can download it.</p>
      </div>
    </div>
    <div class="item">
      <img src="<?= $urlPrefix ?>/img/carousel-5.jpg" alt="Chania">
      <div class="carousel-caption">
        <h3>Reminders</h3>
        <p>Everyone will receive convenient email reminders.</p>
      </div>
    </div>
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
<hr />
<div class="centered">
<p><a class="btn btn-lg btn-success" href="features"><?= Yii::t('frontend','Learn More') ?></a></p>
</div>
