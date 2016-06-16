<?php
use yii\helpers\Html;
use yii\helpers\Url;
use \kartik\switchinput\SwitchInput;

?>
<tr > <!-- panel row -->
  <td >
    <table class="table-list"> <!-- list of places -->
      <tr>
        <td class="table-list-first"> <!-- place name & status -->
          <?= Html::a($model->place->name,Url::to(['meeting/viewplace','id'=>$model->meeting_id,'place_id'=>$model->place_id],true)) ?>
          <?php
            if ($whereStatus[$model->place->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?php
            echo $whereStatus[$model->place->id];
            ?>
          </span><br />
            <?php
            }
          ?>
      </td>
      <td class="table-switches"> <!-- col of switches to float right -->
          <table >
            <tr>
              <td>
                <?php
                // show meeting owner in first column
                   if ($isOwner) {
                     showOwnerStatus($model,$isOwner);
                   } else {
                     showParticipantStatus($model,$isOwner);
                   }
                ?>
              </td>
              <td class="switch-pad">
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
                          'pluginOptions' => [  'size' => 'mini','handleWidth'=>60,'onText' => '<i class="glyphicon glyphicon-ok"></i>&nbsp;choose','onColor'=>'warning','offText'=>'<i class="glyphicon glyphicon-remove"></i>'],
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
</table> <!-- end table list of places -->
</td>
</tr> <!-- end panel row -->
