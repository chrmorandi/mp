<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
?>
Hi, <?= $owner; ?> has invited you to a <?= ($is_activity==Meeting::IS_ACTIVITY?'activity':'meeting')?> <?= $participantList ?> via <?php echo Yii::$app->params['site']['title']; ?> <?php echo MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_HOME,0,$user_id,$auth_key,$site_id); ?>.
<?php echo $intro; ?>
View the invitation here <?php echo $links['view']; ?>
