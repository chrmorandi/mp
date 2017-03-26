<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\FeatureAsset;
FeatureAsset::register($this);

/* @var $this yii\web\View */
$this->title = Yii::$app->params['site']['title'];
?>
<div class="container">
  <div class="row">
     <div class="col-lg-12 text-center">
       <h1>
         <?= Yii::t('frontend','Scheduling Should Be Easy'); ?>
       </h1>
       <p class="lead">
         <?= Yii::t('frontend','Meeting Planner simplifies scheduling between people and groups<br />to help you focus your time on what\'s really important again.'); ?>
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
   <div class="row">
     <div class="col-md-10 col-md-offset-1 ">
  <div class="table-responsive ">
          <div class="membership-pricing-table">
              <table>
                  <tbody><tr>
                      <th></th>
                      <th class="plan-header plan-header-free">
               <div class="pricing-plan-name">Free</div>
               <div class="pricing-plan-price">
                   <sup>$</sup>0<span>.00</span>
               </div>
               <div class="pricing-plan-period">month</div>
               </th>
                  <th class="plan-header plan-header-blue">
                  <div class="pricing-plan-name">Premium</div>
                  <div class="pricing-plan-price">
                    <sup>$</sup>9<span>.99</span>
                </div>
                <div class="pricing-plan-period">month</div>
                  </th>
                  <th class="plan-header plan-header-blue">
                  <div class="pricing-plan-name">Enterprise</div>
                  <div class="pricing-plan-price">
                    <sup>$</sup>24<span>.99</span>
                </div>
                <div class="pricing-plan-period">month</div>
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
                          <!--<a class="btn btn-info">
                              Upgrade
                          </a>-->
                          <em>coming soon</em>
                      </td>
                      <td class="action-header">
                          <!--<a class="btn btn-info">
                              Upgrade
                          </a>-->
                          <em>coming soon</em>
                      </td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','1:1 Meeting and Activity Planning') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Small group meetings (up to 7)') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Schedule With Me Page') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Participant messaging') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Downloadable calendar files') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Email reminders') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Request changes, reschedule & repeat meetings') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Schedule with me landing page') ?></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Group meetings (up to 50)') ?></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Extended planning options') ?></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Google Contacts import') ?></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','SMS reminders and notifications') ?></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Multiple organizers') ?></td>
                      <td></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
                  </tr>
                  <tr>
                      <td><?= Yii::t('frontend','Large group meetings (25+)') ?></td>
                      <td></td>
                      <td></td>
                      <td><span class="icon-yes glyphicon glyphicon-ok-circle"></span></td>
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
          <?= Html::a(Yii::t('frontend','Sign Up Now'),['site/signup'],['class'=>'btn btn-lg btn-success']); ?>
          <a class="btn btn-lg btn-primary" href="/ticket/create" role="button">Questions?</a>
        </div>
</div>
<div class="row">
  <div class="col-lg-12">
<p></p><p>&nbsp;</p>

</div>
