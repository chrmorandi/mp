<?php

use yii\helpers\Html;
use common\components\MiscHelpers;
use yii\widgets\ActiveForm;
use frontend\models\Launch;

/* @var $this yii\web\View */
/* @var $model frontend\models\Launch */
/* @var $form ActiveForm */
?>
<div class="row row-centered">
  <div class="col-xs-12">
    <p class="lead"><?= Yii::t('frontend','Want to wait for the offical launch?');?>
  </div>
</div>
<div class="row row-centered">
  <div class="col-lg-4 col-lg-offset-4 col-xs-6 col-xs-offset-3" id="launchbox">
    <div class="centered">
    <div class="hidden" id="launchResult">
      <p><?= Yii::t('frontend','Thank you! We\'ll get in touch soon!')?></p>
    </div>
    <div class="input-group" id="launch">
      <input type="text" class="form-control" placeholder="email address" id="launch_email">
        <span class="input-group-btn">
          <?= Html::a(Yii::t('frontend','notify me'), 'javascript:void(0);', ['class' => 'btn btn-primary ','title'=>'Notify me at launch','onclick'=>'addLaunchEmail();']); ?>
        </span>
    </div><!-- /input-group -->
  </div>
</div>
<div class="col-xs-3 col-lg-4"></div>
</div>
<input type="hidden"  id="url_prefix" value="<?= MiscHelpers::getUrlPrefix()?>">
