<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use \kartik\switchinput\SwitchInput;
?>

<tr >
  <td >
        <?= Meeting::friendlyDateFromTimestamp($model->start,$timezone) ?>
    <table style="float:right;display:inline;">
  <tr >
  <td >
    <?php
       if ($isOwner) {
         showTimeOwnerStatus($model,$isOwner);
       } /*else {
         showTimeParticipantStatus($model,$isOwner);
       }*/
    ?>
  </td>
  <?php
   /* to do - placeholder for removing cols in planning
   if ($model->status != Meeting::STATUS_PLANNING) {
     ?>
     <?php
   }*/
   ?>
   <td >
       <?php
         if (!$isOwner) {
            showTimeOwnerStatus($model,$isOwner);
          } /*else {
            showTimeParticipantStatus($model,$isOwner);
          }*/
       ?>
   </td>
  <td >
      <?php
      if ($timeCount>1) {
        if ($model->status == $model::STATUS_SELECTED) {
            $value = $model->id;
        }    else {
          $value = 0;
        }
        if ($isOwner || $participant_choose_date_time) {
          // value has to match for switch to be on
          echo SwitchInput::widget([
              'type' => SwitchInput::RADIO,
              'name' => 'time-chooser',
              'items' => [
                  [ 'value' => $model->id],
              ],
              'value' => $value,
              'pluginOptions' => [  'size' => 'mini','handleWidth'=>60,'onText' => 'choose','onColor' => 'warning','offText'=>'<i class="glyphicon glyphicon-remove"></i>'], // '<i class="glyphicon glyphicon-ok"></i>'
              'labelOptions' => ['style' => 'font-size: 12px'],
          ]);
        }
      }
      ?>
  </td>
</tr>
</table>
</td>
</tr>
