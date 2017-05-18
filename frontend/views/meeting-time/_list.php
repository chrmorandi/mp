<?php
  use yii\helpers\Html;
  use frontend\models\Meeting;
?>
<tr> <!-- panel row -->
  <td class="parent-td-table-list">
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
                   if ($timeCount>1) {
                     if ($isOwner) {
                       // show thumbs for owner
                       foreach ($model->meetingTimeChoices as $mtc) {
                         if ($mtc->user_id == $model->meeting->owner_id) {
                            ?>
                           <div class="thumb-choices btn-group" id="mtc-<?= $mtc->id?>" data-toggle="buttons">
                              <label class="btn btn-default <?= ($mtc->status == $mtc::STATUS_YES?'active':'')?>">
                                <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','available');?>"></span>
                              </label>
                              <label class="btn btn-default <?= ($mtc->status == $mtc::STATUS_NO?'active':'')?>">
                                <input type="radio" name="options" autocomplete="off"  value="0"><span class="glyphicon glyphicon-thumbs-down" title="<?= Yii::t('frontend','not available');?>"></span>
                              </label>
                            </div>
                            <?php
                         }
                       }
                     } else {
                       // show thumbs for participants
                       foreach ($model->meetingTimeChoices as $mtc) {
                         if (count($model->meeting->participants)==0) break;
                         if ($mtc->user_id == Yii::$app->user->getId())  {
                             ?>
                             <div class="thumb-choices btn-group" id="mtc-<?= $mtc->id?>" data-toggle="buttons">
                                <label class="btn btn-default <?= ($mtc->status == $mtc::STATUS_YES?'active':'')?>">
                                  <input type="radio" name="options"  autocomplete="off"  value="10"><span class="glyphicon glyphicon-thumbs-up" title="<?= Yii::t('frontend','available');?>"></span>
                                </label>
                                <label class="btn btn-default <?= ($mtc->status == $mtc::STATUS_NO?'active':'')?>">
                                  <input type="radio" name="options" autocomplete="off"  value="0"><span class="glyphicon glyphicon-thumbs-down" title="<?= Yii::t('frontend','not available');?>"></span>
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
  </table> <!-- end table list of times -->
  </td>
  </tr> <!-- end panel row -->
