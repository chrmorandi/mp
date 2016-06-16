<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use \kartik\switchinput\SwitchInput;
?>
<tr > <!-- panel row -->
  <td >
    <table class="table-list"> <!-- list of times -->
      <tr>
        <td class="table-list-first"> <!-- time & status -->
          <?= Meeting::friendlyDateFromTimestamp($model->start,$timezone) ?>
          <?php
            if ($whenStatus['text'][$model->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?php
            echo $whenStatus['text'][$model->id];
            ?>
          </span><br />
            <?php
            }
          ?>
      </td>
      <td class="table-switches"> <!-- col of switches to float right -->
        <table >
          <tr>
              <td >
                <?php
                   if ($isOwner) {
                     showTimeOwnerStatus($model,$isOwner);
                   } else {
                     showTimeParticipantStatus($model,$isOwner);
                   }
                ?>
              </td>
              <td class="switch-pad">
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
                          'pluginOptions' => [  'size' => 'mini','handleWidth'=>60,'onText' => '<i class="glyphicon glyphicon-ok"></i>&nbsp;choose','onColor' => $whenStatus['style'][$model->id],'offText'=>'<i class="glyphicon glyphicon-remove"></i>'],
                          'labelOptions' => ['style' => 'font-size: 12px'],
                      ]);
                    }
                  }
                  ?>
              </td>
            </tr>
          </table>
        </td> <!-- end col with table of switches -->
      </tr>
  </table> <!-- end table list of times -->
  </td>
  </tr> <!-- end panel row -->
