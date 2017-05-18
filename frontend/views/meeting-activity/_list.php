<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Meeting;
?>
<tr > <!-- panel row -->
  <td class="parent-td-table-list">
    <table class="table-list"> <!-- list of activitys -->
      <tr>
        <td class="table-list-first"> <!-- activity & status -->
          <?= Html::a(Html::encode($model->activity),Url::to(['meeting/viewactivity','id'=>$model->meeting_id,'activity_id'=>$model->id],true)) ?>
          <?php
            if ($activityStatus['text'][$model->id]<>'') {
            ?>
            <br /><span class="smallStatus">
            <?= HTML::decode($activityStatus['text'][$model->id]);
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
                if ($activityCount>1) {
                   if ($isOwner) {
                     foreach ($model->meetingActivityChoices as $mac) {
                       if ($mac->user_id == $model->meeting->owner_id) {
                         ?>
                        <div class="thumb-choices btn-group" id="mac-<?= $mac->id?>" data-toggle="buttons">
                           <label class="btn btn-default <?= ($mac->status == $mac::STATUS_YES?'active':'')?>">
                             <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','acceptable');?>"></span>
                           </label>
                           <label class="btn btn-default <?= ($mac->status == $mac::STATUS_NO?'active':'')?>">
                             <input type="radio" name="options" autocomplete="off"  value="0"><span class="glyphicon glyphicon-thumbs-down" title="<?= Yii::t('frontend','not acceptable');?>"></span>
                           </label>
                         </div>
                         <?php
                       }
                     }
                   } else {
                     foreach ($model->meetingActivityChoices as $mac) {
                       if (count($model->meeting->participants)==0) break;
                       if ($mac->user_id == Yii::$app->user->getId())  {
                         ?>
                        <div class="thumb-choices btn-group" id="mac-<?= $mac->id?>" data-toggle="buttons">
                           <label class="btn btn-default <?= ($mac->status == $mac::STATUS_YES?'active':'')?>">
                             <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','acceptable');?>"></span>
                           </label>
                           <label class="btn btn-default <?= ($mac->status == $mac::STATUS_NO?'active':'')?>">
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
  </table> <!-- end table list of activitys -->
  </td>
  </tr> <!-- end panel row -->
