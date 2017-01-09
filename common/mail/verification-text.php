<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
Hi <?php echo \common\components\MiscHelpers::getDisplayName($user->id); ?>,
Please click the button below to verify your email address:
<?php echo $verifyLink; ?>
