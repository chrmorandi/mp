<?php
  use yii\helpers\Html;
  use frontend\models\Meeting;
  use \kartik\switchinput\SwitchInput;
?>
<tr > <!-- panel row -->
  <td >
    <table class="table-list"> <!-- list of times -->
      <tr>
        <td class="table-list-first" id="t_id_<?= $model->id ?>_<?= $model->start ?>"> <!-- time & status -->
          <?= Html::a(Meeting::friendlyDateFromTimestamp($model->start,$timezone),['/meeting-time/view','id'=>$model->id]); ?>
          <?php
            if ($whenStatus['text'][$model->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?= $whenStatus['text'][$model->id] ?>
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
                     foreach ($model->meetingTimeChoices as $mtc) {
                       if ($mtc->user_id == $model->meeting->owner_id) {
                           if ($mtc->status == $mtc::STATUS_YES)
                             $value = 1;
                           else
                             $value =0;
                             /*echo SwitchInput::widget([
                             'type' => SwitchInput::CHECKBOX,
                             'name' => 'meeting-time-choice',
                             'id'=>'mtc-'.$mtc->id,
                             'value' => $value,
                             'disabled' => !$isOwner,
                             'pluginOptions' => ['size' => 'small','labelWidth'=>1,'handleWidth'=>50,'onText' => '<i class="glyphicon glyphicon-thumbs-up"></i>&nbsp;'.Yii::t('frontend','yes'),'offText'=>'<i class="glyphicon glyphicon-thumbs-down"></i>&nbsp;'.Yii::t('frontend','no'),'onColor' => 'success','offColor' => 'danger',],
                           ]);*/
                           echo '<div class="btn-group" data-toggle="buttons"><label class="btn btn-default ">
                             <input type="radio" autocomplete="off" > <div class="">Meet then</div>
                           </label>
                           </div>
                           <div class="btn-group" data-toggle="buttons">
                           <label class="btn btn-default ">
                             <input type="radio" autocomplete="off" checked> <span class="glyphicon glyphicon-heart"></span>
                           </label>
                           <label class="btn btn-default active">
      <input type="radio" name="options" id="option1" autocomplete="off">Available
    </label>
    <label class="btn btn-default">
      <input type="radio" name="options" id="option2" autocomplete="off"><span class="glyphicon glyphicon-remove"></span>
    </label>
  </div>';
                       }
                     }
                   } else {
                     foreach ($model->meetingTimeChoices as $mtc) {
                       if (count($model->meeting->participants)==0) break;
                       if ($mtc->user_id == Yii::$app->user->getId())  {
                           if ($mtc->status == $mtc::STATUS_YES)
                             $value = 1;
                           else if ($mtc->status == $mtc::STATUS_NO)
                             $value =0;
                           else if ($mtc->status == $mtc::STATUS_UNKNOWN)
                             $value =-1;
                           echo SwitchInput::widget([
                             'type' => SwitchInput::CHECKBOX,
                             'name' => 'meeting-time-choice',
                             'id'=>'mtc-'.$mtc->id,
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
  </table> <!-- end table list of times -->
  </td>
  </tr> <!-- end panel row -->
