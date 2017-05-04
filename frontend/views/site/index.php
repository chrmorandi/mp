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
            <h1><?= Yii::t('frontend','Simpler Scheduling'); ?></h1>
                <p class="lead"><?= Yii::t('frontend','Choose times and places together. Make planning easy.'); ?></p>
                <div class="centered">
                  <p><a class="btn btn-lg btn-success" href="features"><?= Yii::t('frontend','Learn more') ?></a></p>
                </div>
          </div> <!-- end jumbo -->
      </div>
      <div class="col-md-3 ">
          <div class="panel panel-default">
              <div class="panel-heading">
                <strong><?php echo Yii::t('frontend','Schedule Your First Meeting'); ?></strong>
              </div>
              <div class="panel-body panel-auth-clients">
                  <?php $authAuthChoice = AuthChoice::begin([
                    'baseAuthUrl' => ['site/auth','mode'=>'signup'],
                    'popupMode' => false,
                ]); ?>
                <?= Yii::t('frontend','Connect with the following services:'); ?><br /><br />
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
          <div class="panel panel-default">
            <div class="panel-heading">
              <strong><?php echo Yii::t('frontend','Wait for the Official Launch'); ?></strong>
            </div>
            <div class="panel-body">
              <?= $this->render('launch');?>
            </div>
          </div>
      </div>
  </div>
</div>
