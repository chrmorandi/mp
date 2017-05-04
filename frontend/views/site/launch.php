<?php

use yii\helpers\Html;
use common\components\MiscHelpers;
use yii\widgets\ActiveForm;
use frontend\models\Launch;

/* @var $this yii\web\View */
/* @var $model frontend\models\Launch */
/* @var $form ActiveForm */
?>
  <div id="launchbox">
    <div class="centered">
    <div class="hidden" id="launchResult">
      <p><?= Yii::t('frontend','Thank you! We\'ll get in touch soon!')?></p>
    </div>
    <div class="input-group" id="launch">
      <input type="text" class="form-control" placeholder="<?= Yii::t('frontend','email address') ?>" id="launch_email">
        <span class="input-group-btn">
          <?= Html::a(Yii::t('frontend','notify me'), 'javascript:void(0);', ['class' => 'btn btn-primary ','title'=>Yii::t('frontend','Notify me at launch'),'onclick'=>'addLaunchEmail();']); ?>
        </span>
    </div><!-- /input-group -->
  </div>
</div>
