<?php
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Meeting;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>
<table  cellpadding="0" cellspacing="10" border="0" align="center" width="600">
  <tr>
    <td colspan="2">
      <p><em>Hi, <?= $owner ?> is inviting you to an event using a new service called <?= HTML::a(Yii::t('frontend','Meeting Planner'),Url::home(true)) ?>. The service makes it easy to plan meetings without the exhausting threads of repetitive emails. Please try it out below.</em></p>
      <p><?= $intro ?></p>
      <p> <?= HTML::a(Yii::t('frontend','Visit the Meeting page'),$links['view']) ?></a>
      | <?= HTML::a(Yii::t('frontend','Accept all places and times'),$links['acceptall']) ?>
         <?php
         if ($meetingSettings->participant_finalize && count($places)==1 && count($times)==1) { ?>
         | <?= HTML::a(Yii::t('frontend','Finalize meeting'),$links['finalize']) ?>
         <?php
         }
         ?>
         | <?= HTML::a(Yii::t('frontend','Decline invitation'),$links['cancel']) ?></p>
    </td>
  </tr>
  <tr style="border-bottom:1px solid #ccc;">
    <td width="300"><strong>Where</strong></td>
    <td width="300" >
      <?= HTML::a(Yii::t('frontend','accept all places'),$links['acceptplaces']) ?>
      <?php
      if ($meetingSettings->participant_add_place) { ?>
      | <?= HTML::a(Yii::t('frontend','suggest a place'),$links['addplace']) ?>
      <?php
      }
      ?>
    </td>
  </tr>
<?php
  foreach($places as $p) {
    ?>
    <tr>
      <td width="300">
        <p>
        <?= $p->place->name ?>
        <br/ >
        <span style="font-size:75%;"><?= $p->place->vicinity ?> <?= HTML::a(Yii::t('frontend','view map'),'http://www.google.com/maps/search/'.$p->place->name.','.$p->place->full_address) ?></span>
      </p>
    </td>
    <td width="300" >
      <?= HTML::a(Yii::t('frontend','accept'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>Meeting::COMMAND_ACCEPT_PLACE,'obj_id'=>$p->id,'actor_id'=>$participant_id],true)) ?> | <?= HTML::a(Yii::t('frontend','reject'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>Meeting::COMMAND_REJECT_PLACE,'obj_id'=>$p->id,'actor_id'=>$participant_id],true)) ?>
      <?php
        if ($meetingSettings->participant_choose_place) { ?>
        | <?= HTML::a(Yii::t('frontend','choose'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>Meeting::COMMAND_CHOOSE_PLACE,'obj_id'=>$p->id,'actor_id'=>$participant_id],true)) ?>
        <?php
        }
        ?>
    </td>
    </tr>
        <?
      }
  ?>
  <tr>
    <td width="300"><br /></td><td width="300"></td>
  </tr>
  <tr style="border-bottom:1px solid #ccc;">
    <td width="300">
      <strong>When</strong><br />
    </td>
      <td width="300" >
        <?= HTML::a(Yii::t('frontend','accept all times'),$links['accepttimes']) ?>
        <?php
        if ($meetingSettings->participant_add_date_time) { ?>
        | <?= HTML::a(Yii::t('frontend','suggest a time'),$links['addtime']) ?>
        <?php
        }
        ?>
      </td>
  </tr>
<?php
  foreach($times as $t) {
    ?>
    <tr>
      <td width="300">
        <p><?= Meeting::friendlyDateFromTimestamp($t->start) ?></p>
      </td>
      <td width="300">
        <?= HTML::a(Yii::t('frontend','accept'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>'accepttime','val'=>$p->id,'p'=>$participant_id],true)) ?> | <?= HTML::a(Yii::t('frontend','reject'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>'rejecttime','val'=>$p->id,'p'=>$participant_id],true)) ?>
        <?php
          if ($meetingSettings->participant_choose_date_time) { ?>
          | <?= HTML::a(Yii::t('frontend','choose'),Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>'choosetime','val'=>$p->id,'p'=>$participant_id],true)) ?>
          <?php
          }
          ?>
      </td>
      </tr>
        <?
      }
  ?>
  <tr>
    <td width="300"><br /></td><td width="300"></td>
  </tr>
<?php
  if (count($notes)>0) {
    ?>
      <tr style="border-bottom:1px solid #ccc;">
        <td width="300" ><strong>Notes</strong></td>
        <td width="300" >
            <?= HTML::a(Yii::t('frontend','add a note'),$links['addnote']) ?>
        </td>
      </tr>
<?php
  foreach($notes as $n) {
    ?>
    <tr>
      <td colspan="2">
        <p><em><?= $n->postedBy->email ?> says: </em>
        "<?= $n->note ?>"
      </p><br/ >
      </td>
    </tr>
        <?
      }
  ?>
</table>
  <?
  }
  ?>
<table  cellpadding="0" cellspacing="10" border="0" align="center" width="600">
  <tr><td width="300" style="text-align:center;margin:10px;">
<p>
  <?= Html::a(Yii::t('frontend','Visit Meeting Planner'), Url::home(true)) ?>
</p>
</td></tr>
<tr><td width="300" style="text-align:center;font-size:75%;margin:10px;">
<em>
  <?= HTML::a(Yii::t('frontend','Review your email settings'),Url::to(['/site/unavailable'],true)) ?>
  | <?= HTML::a(Yii::t('frontend','Block this person'),Url::to(['/site/unavailable'],true)) ?>
  | <?= HTML::a(Yii::t('frontend','Block all emails'),Url::to(['/site/unavailable'],true)) ?>
</em>
</td></tr>
</table>
