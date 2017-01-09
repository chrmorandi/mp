<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
?>
Hi <?php echo MiscHelpers::getDisplayName($user_id); ?>.
We don't have any contact information for you for your upcoming meeting, <?php echo $links['view']; ?>.
Please click <?php echo $links['add_contact'] ?> to add your phone number or online conferencing details.
