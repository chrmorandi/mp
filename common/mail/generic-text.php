<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?= Html::encode($content['plain_text']); ?>
<?php if (isset($links['button_url'])) { ?>
  &nbsp;-&nbsp;
  <?= Html::encode($links['button_url']); ?>
<?php } ?>
