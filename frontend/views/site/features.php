<?php
use yii\helpers\Html;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\FeatureAsset;
FeatureAsset::register($this);

/* @var $this yii\web\View */
$this->title = 'Meeting Planner';
?>
<div class="container">
  <div class="row">
     <div class="col-lg-12 text-center">
       <h1>
         <?= Yii::t('frontend','Features'); ?>
       </h1>
       <p class="lead">
         <?= Yii::t('frontend','Meeting Planner makes scheduling with just one or many people as easy <br />as it should be letting you focus your time on what\'s really important again.'); ?>
        </p>
     </div>
</div>
     <div class="row marketing">
       <div class="col-lg-4 col-lg-offset-2">

         <h4><?= Yii::t('frontend','Scheduling'); ?></h4>
         <p><?= Yii::t('frontend','Plan meetings with friends and colleagues in minutes, without the back and forth emails.'); ?></p>

         <h4><?= Yii::t('frontend','Choosing'); ?></h4>
         <p><?= Yii::t('frontend','Participants select times and dates that work well for them. The organizer chooses '); ?></p>

         <h4><?= Yii::t('frontend','Calendar Integration'); ?></h4>
         <p><?= Yii::t('frontend','Downloadable calendar files make it easy to add meetings to your calendar of choice.'); ?></p>

         <h4><?= Yii::t('frontend',''); ?></h4>
         <p><?= Yii::t('frontend','.'); ?></p>
       </div>

       <div class="col-lg-4">
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

   </div>
   <div class="row">
     <div class="col-lg-12">
  <hr />

   </div>
   </div>
   <div class="row">
     <div class="col-md-10 col-md-offset-1 ">
  <div class="table-responsive ">
          <div class="membership-pricing-table">
              <table>
                  <tbody><tr>
                      <th></th>
                      <th class="plan-header plan-header-free">
                  <div class="pricing-plan-name">Free</div>
                  </th>
                  <th class="plan-header plan-header-blue">
                  <div class="pricing-plan-name">Premium</div>
                  </th>
                  </tr>
                  <tr>
                      <td></td>

                      <td class="action-header">
                          <a class="btn btn-info">
                              Sign Up
                          </a>
                      </td>
                      <td class="action-header">
                          <a class="btn btn-info">
                              Upgrade
                          </a>
                      </td>
                  </tr>
                  <tr>
                      <td>Tutorials and Support Docs:</td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td>Support Forum Access:</td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                  </tr>
                  <tr>
                      <td>Automatic Updates:</td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                  </tr>
                  <tr>
                      <td>Unlock rewards:</td>
                      <td><span class="icon-no glyphicon glyphicon-remove-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td>Skills:</td>
                      <td>20</td>
                      <td>30</td>
                  </tr>
                  <tr>
                      <td>Websites:</td>
                      <td>1</td>
                      <td>5</td>
                  </tr>
              </tbody></table>
          </div>
      </div>
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
          <a class="btn btn-lg btn-success" href="/site/signup" role="button">Sign Up Now</a>
          <a class="btn btn-lg btn-primary" href="http://support.meetingplanner.io" role="button">Questions?</a>
        </div>
</div>
<div class="row">
  <div class="col-lg-12">
<p></p><p>&nbsp;</p>

</div>
