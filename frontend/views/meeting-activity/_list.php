<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use \kartik\switchinput\SwitchInput;
?>
<tr > <!-- panel row -->
  <td >
    <table class="table-list"> <!-- list of activitys -->
      <tr>
        <td class="table-list-first"> <!-- activity & status -->
          something here
          <?php
            if ($activityStatus['text'][$model->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?php
            echo $activityStatus['text'][$model->id];
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
                     foreach ($model->meetingActivityChoices as $mac) {
                       if ($mac->user_id == $model->meeting->owner_id) {
                           if ($mac->status == $mac::STATUS_YES)
                             $value = 1;
                           else
                             $value =0;
                             echo SwitchInput::widget([
                             'type' => SwitchInput::CHECKBOX,
                             'name' => 'meeting-activity-choice',
                             'id'=>'mac-'.$mac->id,
                             'value' => $value,
                             'disabled' => !$isOwner,
                             'pluginOptions' => ['size' => 'small','labelWidth'=>1,'handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no','onColor' => 'success','offColor' => 'danger',],
                             ]);
                       }
                     }
                   } else {
                     foreach ($model->meetingActivityChoices as $mac) {
                       if (count($model->meeting->participants)==0) break;
                       if ($mac->user_id == Yii::$app->user->getId())  {
                           if ($mac->status == $mac::STATUS_YES)
                             $value = 1;
                           else if ($mac->status == $mac::STATUS_NO)
                             $value =0;
                           else if ($mac->status == $mac::STATUS_UNKNOWN)
                             $value =-1;
                           echo SwitchInput::widget([
                             'type' => SwitchInput::CHECKBOX,
                             'name' => 'meeting-activity-choice',
                             'id'=>'mac-'.$mac->id,
                             'tristate'=>true,
                             'indeterminateValue'=>-1,
                             'indeterminateToggle'=>false,
                             'disabled'=>$isOwner,
                             'value' => $value,
                             'pluginOptions' => ['size' => 'small','labelWidth'=>1,'handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;yes','offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;no','onColor' => 'success','offColor' => 'danger',],
                         ]);
                       }
                     }
                   }
                ?>
              </td>

            </tr>
          </table>
        </td> <!-- end col with table of switches -->
      </tr>
  </table> <!-- end table list of activitys -->
  </td>
  </tr> <!-- end panel row -->
