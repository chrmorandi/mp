<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
?>
Hi, <?php echo Yii::t('frontend','Just a reminder about your upcoming meeting ').$display_time; ?> via <?php echo Yii::$app->params['site']['title'] ?>.
<?php if ($chosen_place!==false) { ?>
The meeting is at <?= $chosen_place->place->name ?>
  (<?php echo $chosen_place->place->vicinity; ?>,
  <?php
} else {
?>
The meeting is via phone or video conference.
<?php
  }
?>
<br />
Click below to view more details about the meeting:
<?php echo $links['view'] ?>
