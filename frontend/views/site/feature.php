<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
//use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = 'Meeting Planner';
?><div class="container">
  <div class="row">
     <div class="col-lg-12 text-center">
       <h1 class="display-3">
         <?= Yii::t('frontend','Features'); ?>
       </h1>
       <p class="lead">
         <?= Yii::t('frontend','Meeting Planner makes scheduling with just one or many people as easy <br />as it should be letting you focus your time on what\'s really important again.'); ?>
        </p>
     </div>
</div>
     <div class="row marketing">
       <div class="col-lg-6">
         <h4><?= Yii::t('frontend','Scheduling'); ?></h4>
         <p><?= Yii::t('frontend','Plan meetings with friends and colleagues in minutes, without the back and forth emails.'); ?></p>

         <h4><?= Yii::t('frontend','Choosing'); ?></h4>
         <p><?= Yii::t('frontend','Participants select times and dates that work well for them. The organizer chooses '); ?></p>

         <h4><?= Yii::t('frontend','Calendar Integration'); ?></h4>
         <p><?= Yii::t('frontend','Downloadable calendar files make it easy to add meetings to your calendar of choice.'); ?></p>

         <h4><?= Yii::t('frontend',''); ?></h4>
         <p><?= Yii::t('frontend','.'); ?></p>
       </div>

       <div class="col-lg-6">
         <h4><?= Yii::t('frontend','Groups'); ?></h4>
         <p><?= Yii::t('frontend','Invite many people to select their availability.'); ?></p>

         <h4><?= Yii::t('frontend','Invite by Secure Link'); ?></h4>
         <p><?= Yii::t('frontend','If you wish, you can provide a secure URL with participants via email.'); ?></p>

         <h4><?= Yii::t('frontend','Multiple Organizers'); ?></h4>
         <p><?= Yii::t('frontend','Grant any participant organizing powers.'); ?></p>

         <h4><?= Yii::t('frontend',''); ?></h4>
         <p><?= Yii::t('frontend','.'); ?></p>
       </div>
     </div>
     <div class="row  ">
       <div class="col-md-12 text-center">
         <a class="btn btn-lg btn-success" href="/site/signup" role="button">Sign Up Today</a>
         <a class="btn btn-lg btn-primary" href="http://support.meetingplanner.io" role="button">Questions?</a>
       </div>

   </div>
</div>
