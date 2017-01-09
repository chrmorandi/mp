<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
?>
Hi <?php echo MiscHelpers::getDisplayName($user_id); ?>, changes have been made to your meeting. at <?php echo Yii::$app->params['site']['title'] ?>.
Please click the link to view the meeting page, <?php echo $links['view'] ?>.
