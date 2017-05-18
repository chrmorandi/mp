<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\FeatureAsset;
FeatureAsset::register($this);

/* @var $this yii\web\View */
$this->title = Yii::t('frontend',Yii::$app->params['site']['title']);
?>
<div class="container">
  <div class="row">
     <div class="col-lg-12 text-center">
       <h1>
         <?= Yii::t('frontend','Scheduling Should Be Easy'); ?>
       </h1>
       <p class="lead">
         <?= $this->title.' '.Yii::t('frontend','simplifies scheduling between people and groups<br />to help you focus your time on what\'s really important again.'); ?>
        </p>
     </div>
</div>
     <div class="row marketing">
       <div class="col-lg-4 col-lg-offset-2">

         <h4><?= Yii::t('frontend','Scheduling'); ?></h4>
         <p><?= Yii::t('frontend','Plan meetings with friends and colleagues in minutes, without the needless back and forth email chains.'); ?></p>

         <h4><?= Yii::t('frontend','Activities'); ?></h4>
         <p><?= Yii::t('frontend','Organize social meetups with friends and collaboratively choose what to do e.g. dancing, movies, skiing, et al.'); ?></p>

         <h4><?= Yii::t('frontend','Choosing Dates, Times & Places'); ?></h4>
         <p><?= Yii::t('frontend','Participants select times and dates that work well for them. Organizers choose the best.'); ?></p>

         <h4><?= Yii::t('frontend','Calendar Integration'); ?></h4>
         <p><?= Yii::t('frontend','Downloadable calendar files make it easy to add meetings to your calendar of choice.'); ?></p>
         </div>

       <div class="col-lg-4">
         <h4><?= Yii::t('frontend','Groups'); ?></h4>
         <p><?= Yii::t('frontend','Invite more people and easily find the most available times and places.'); ?></p>

         <h4><?= Yii::t('frontend','Invite by Secure Link'); ?></h4>
         <p><?= Yii::t('frontend','If you wish, you can share a secure URL with participants via email.'); ?></p>

         <h4><?= Yii::t('frontend','Multiple Organizers'); ?></h4>
         <p><?= Yii::t('frontend','Grant any participant organizing powers.'); ?></p>

         <h4><?= Yii::t('frontend','Schedule With Me'); ?></h4>
         <p><?= Yii::t('frontend','Share your Schedule With Me page to make it easy for friends, colleagues, clients et al.'); ?></p>
       </div>
     </div>
   </div>
   <div class="row">
     <div class="col-lg-12">
       <hr />
     </div>
   </div>
   <div class="row  ">
     <div class="col-md-12 text-center">
       <p></p>
       <?= Html::a(Yii::t('frontend','Register'),['site/signup'],['class'=>'btn btn-lg btn-success']); ?>
       <?= Html::a(Yii::t('frontend','Questions?'),['ticket/create'],['class'=>'btn btn-lg btn-primary']); ?>
     </div>
   </div>

   <div class="row">
     <div class="col-lg-12">
       <hr />
     </div>
   </div>
   <div class="row video-top">
       <div class="col-md-12">
         <?= $this->render('_video_carousel.php',['urlPrefix'=>$urlPrefix]);?>
       </div>
   </div>
   <div class="row">
     <div class="col-lg-12">
  <hr />
   </div>
   </div>
   <?= $this->render('_feature_table.php');?>
