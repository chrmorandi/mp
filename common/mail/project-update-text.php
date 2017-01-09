<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
?>
<?php echo $msg->subject; ?> <?php echo $msg->caption; ?> <?php echo $msg->content; ?>
 <?php echo $msg->action_text; ?> by clicking here, <?php echo $links['action_url'] ?>.
