<?php

use yii\helpers\Html;

?>

<tr >
  <td style >
    <div class="meeting-participant-view">
      <div>
        <?php echo $model->participant->email ?>
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
