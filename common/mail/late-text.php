<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
?>
Just a note that <?php echo $sender_name; ?> is running a few minutes late for your meeting.<br />
Click the link to view the meeting page: <?php echo $links['view'] ?>.
