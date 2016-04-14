<?php
use yii\helpers\Html;
use yii\helpers\Url;
use \kartik\switchinput\SwitchInput;

?>

<tr >
  <td style >
        <?= Html::a($model->place->name,Url::to(['meeting/viewplace','id'=>$model->meeting_id,'meeting_place_id'=>$model->id],true)) ?>
  </td>
  <td style>
    <?php
    // show meeting owner in first column
       if ($isOwner) {
         showOwnerStatus($model,$isOwner);
       } else {
         showParticipantStatus($model,$isOwner);
       }
    ?>

  </td>
  <td style>
      <?php
    // show meeting participants in next column(s)
        if (!$isOwner) {
           showOwnerStatus($model,$isOwner);
         } else {
           showParticipantStatus($model,$isOwner);
         }
      ?>
  </td>
  <td style>
      <?php
      if ($placeCount>1) {
        if ($model->status == $model::STATUS_SELECTED) {
            $value = $model->id;
        }    else {
          $value = 0;
        }
        if ($isOwner || $participant_choose_place) {
          // value has to match for switch to be on
          echo SwitchInput::widget([
            'type' => SwitchInput::RADIO,
            'name' => 'place-chooser',
              'items' => [
                  [ 'value' => $model->id],
              ],
              'value' => $value,
              'pluginOptions' => [  'size' => 'mini','handleWidth'=>60,'onText' => '<i class="glyphicon glyphicon-ok"></i>','offText'=>'<i class="glyphicon glyphicon-remove"></i>'],
              'labelOptions' => ['style' => 'font-size: 12px'],
          ]);
        }
      }
      ?>
  </td>
</tr>
