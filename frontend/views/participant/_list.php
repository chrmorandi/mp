<?php
use yii\helpers\Html;
use \common\components\MiscHelpers;
?>
<tr >
  <td style >
    <div class="meeting-participant-view">
      <div>
        <?php echo MiscHelpers::getDisplayName($model->participant->id); ?>
        <?php
          if ($model->participant->status == \frontend\models\Participant::STATUS_DECLINED) {
            echo Yii::t('frontend','(declined)');
          }
        ?>
        </div>
      </div>
</td>
</tr>
</div>
