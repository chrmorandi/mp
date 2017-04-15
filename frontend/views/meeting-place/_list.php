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
            if ($whereStatus['text'][$model->place->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?php
            echo $whereStatus['text'][$model->place->id];
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
                     foreach ($model->meetingPlaceChoices as $mpc) {
                       if ($mpc->user_id == $model->meeting->owner_id) {
                           if ($mpc->status == $mpc::STATUS_YES)
                             $value = 1;
                           else
                             $value =0;
                           echo SwitchInput::widget([
                           'type'=>SwitchInput::CHECKBOX,
                           'name' => 'meeting-place-choice',
                           'id'=>'mpc-'.$mpc->id,
                           'value' => $value,
                           'disabled' => !$isOwner,
                           // prev: 75, acceptable
                           'pluginOptions' => ['size' => 'small','labelWidth'=>1,'handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;'.Yii::t('frontend','yes'),'offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;'.Yii::t('frontend','no'),'onColor' => 'success','offColor' => 'danger',],
                           ]);
                       }
                     }
                   } else {
                     foreach ($model->meetingPlaceChoices as $mpc) {
                       if (count($model->meeting->participants)==0) break;
                       if ($mpc->user_id == Yii::$app->user->getId()) {
                           if ($mpc->status == $mpc::STATUS_YES)
                             $value = 1;
                           else if ($mpc->status == $mpc::STATUS_NO)
                             $value =0;
                           else if ($mpc->status == $mpc::STATUS_UNKNOWN)
                             $value =-1;
                           echo SwitchInput::widget([
                             'type'=>SwitchInput::CHECKBOX,
                             'name' => 'meeting-place-choice',
                             'id'=>'mpc-'.$mpc->id,
                             'tristate'=>true,
                             'indeterminateValue'=>-1,
                             'indeterminateToggle'=>false,
                             'disabled'=>$isOwner,
                             'value' => $value,
                             'pluginOptions' => ['size' => 'small','labelWidth'=>1,'handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;'.Yii::t('frontend','yes'),'offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;'.Yii::t('frontend','no'),'onColor' => 'success','offColor' => 'danger',],
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
</table> <!-- end table list of places -->
</td>
</tr> <!-- end panel row -->
