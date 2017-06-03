<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;

//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::$app->params['site']['title'];
?>
<div class="site-index ">
  <div class="row home-top">
      <div class="col-md-6 col-md-offset-1">
        <div class="jumbotron">
            <h1><?= Yii::t('frontend','Dating made easy'); ?></h1>
                <p class="lead"><?= Yii::t('frontend','choose times and places without all the emails'); ?></p>
                <div class="centered">
                  <p><?= Html::a(Yii::t('frontend','Get Started'),['site/signup'], ['class' => 'btn btn-lg btn-success','title'=>Yii::t('frontend','schedule your first meeting')]); ?></p>
                </div>
          </div> <!-- end jumbo -->
      </div>
      <div class="col-md-3 ">
          <div class="panel panel-default">
              <div class="panel-heading" style="font-size:1.33em;">
                <strong><?php echo Yii::t('frontend','Plan your first date'); ?></strong>
              </div>
              <div class="panel-body panel-auth-clients centered">
                  <?php $authAuthChoice = AuthChoice::begin([
                    'baseAuthUrl' => ['site/auth','mode'=>'signup'],
                    'popupMode' => false,
                ]); ?>
                <?= Yii::t('frontend','Start instantly with any of these services:'); ?><br /><br />
                <ul class="auth-clients" >
                <?php foreach ($authAuthChoice->getClients() as $client): ?>
                    <li class="auth-client"><?php $authAuthChoice->clientLink($client) ?></li>
                <?php endforeach; ?>
                </ul>
                <?php AuthChoice::end(); ?>
              </div>
              <div class="panel-footer">
                  Or, <?= HTML::a(Yii::t('frontend','sign up using your email address'),['site/signup']); ?>
                </div>
          </div>
      </div>
  </div>
</div>
<?= $this->render('home-tour_fd') ?>
