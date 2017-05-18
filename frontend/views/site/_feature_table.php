<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\authclient\widgets\AuthChoice;
use frontend\assets\FeatureAsset;
FeatureAsset::register($this);

/* @var $this yii\web\View */
$this->title = Yii::$app->params['site']['title'];
?>
   <div class="row">
     <div class="col-md-10 col-md-offset-1 col-lg-12">
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
                              <?= Html::a(Yii::t('frontend','Register'),['site/signup'],['class'=>'btn btn-info']) ?>
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
              </tbody></table>
          </div>
      </div>
    </div>
  </div>
