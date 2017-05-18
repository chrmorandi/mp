<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<tr > <!-- panel row -->
  <td class="parent-td-table-list">
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
                if ($placeCount>1) {
                   if ($isOwner) {
                     foreach ($model->meetingPlaceChoices as $mpc) {
                       if ($mpc->user_id == $model->meeting->owner_id) {
                         ?>
                        <div class="thumb-choices btn-group" id="mpc-<?= $mpc->id?>" data-toggle="buttons">
                           <label class="btn btn-default <?= ($mpc->status == $mpc::STATUS_YES?'active':'')?>">
                             <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','acceptable');?>"></span>
                           </label>
                           <label class="btn btn-default <?= ($mpc->status == $mpc::STATUS_NO?'active':'')?>">
                             <input type="radio" name="options" autocomplete="off"  value="0"><span class="glyphicon glyphicon-thumbs-down" title="<?= Yii::t('frontend','not acceptable');?>"></span>
                           </label>
                         </div>
                         <?php
                       }
                     }
                   } else {
                     foreach ($model->meetingPlaceChoices as $mpc) {
                       if (count($model->meeting->participants)==0) break;
                       if ($mpc->user_id == Yii::$app->user->getId()) {
                         ?>
                        <div class="thumb-choices btn-group" id="mpc-<?= $mpc->id?>" data-toggle="buttons">
                           <label class="btn btn-default <?= ($mpc->status == $mpc::STATUS_YES?'active':'')?>">
                             <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','acceptable');?>"></span>
                           </label>
                           <label class="btn btn-default <?= ($mpc->status == $mpc::STATUS_NO?'active':'')?>">
                             <input type="radio" name="options" autocomplete="off"  value="0"><span class="glyphicon glyphicon-thumbs-down" title="<?= Yii::t('frontend','not acceptable');?>"></span>
                           </label>
                         </div>
                         <?php
                       }
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
