<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
?>
Hi <?=  $owner; ?>,
<?php
  $meeting_label=($is_activity==Meeting::IS_ACTIVITY?'Activity':'Meeting');
  if (!$reopened) {
      echo Yii::t('frontend','Your '.$meeting_label.' is Planned!');
  } else {
      echo Yii::t('frontend','Your '.$meeting_label.' Has Been Modified!');
  }
  ?>
 <?= $participantList ?> via <?php echo Yii::$app->params['site']['title'] ?>, <?php echo MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_HOME,0,$user_id,$auth_key,$site_id); ?>. Add this event to your calendar by opening the attachment below or download it here, <?php echo $links['download']; ?>.
