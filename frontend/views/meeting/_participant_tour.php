<?php
use yii\helpers\Html;
$tourButtons=[
  Yii::t('frontend','Back'),
  Yii::t('frontend','Next'),
  Yii::t('frontend','Close')
];
// to do - enable activity button conditionally
// prePlanningTour
// postPlanningTour
//array_merge(prePlanningTour, $cond ? array('k2' => 'v2') : [], postPlanningTour)
$tour=[
[
  '.nav-tabs top',
  Yii::t('frontend','Welcome'),
  Yii::t('frontend','Allow me to show you how to plan a . <p>If you prefer, you can <a href="javascript::return false;" onclick="turnOffGuide();">turn off this guide</a>.<br /><br />')],
[
  '#headingWho top',
  Yii::t('frontend','Who would you like to invite?'),
  'You can add one person or a group of people to your . <p>Click <a href="javascript:void(0);" onclick="showWhoEmail();"><span class="glyphicon glyphicon-user btn-primary mini-button"></span></a> to add participants.</p>'],
[
  '#invitation-url bottom',
  Yii::t('frontend','Sharing the link'),
  'Or, you can email the meeting link to people directly'
],
[
  '#headingWhat bottom',
  Yii::t('frontend','What is your meeting about?'),
  'You can customize the subject of your  for the invitation email.<p>Click <a href="javascript:void(0);" onclick="showWhat();"><span class="glyphicon glyphicon-pencil btn-primary mini-button"></span></a> to edit the subject.</p>'
],
[
  '#headingWhen top',
  Yii::t('frontend','When do you want to meet?'),
  'Suggest dates and times for your . With more than one, people can help you choose. <p>Click <a href="javascript:void(0);" onclick="$(\'#buttonTime\' ).trigger(\'click\');"><span class="glyphicon glyphicon-plus btn-primary mini-button"></span></a> to add them.</p>'
],
[
  '#headingWhere top',
  Yii::t('frontend','Where do you want to meet?'),
  'Suggest places for your . With multiple places, people can help you choose. <p>Click <a href="javascript:void(0);" onclick="showWherePlaces();"><span class="glyphicon glyphicon-plus btn-primary mini-button"></span></a> to add them.</p>'
],
[
  '#virtualThingBox top',
  Yii::t('frontend','Is this a virtual meeting?'),
  'Switch between <em>in person</em> and <em>virtual</em> s such as phone calls or online conferences.'
],
[
  '#actionSend top',
  Yii::t('frontend','Sending invitations'),
  'Scheduling is collaborative. After you add times and places, you can <strong>Invite</strong> participants to select their favorites. <em>A place isn\'t necessary for virtual \s.</em>'
],
[
  '#actionFinalize right',
  Yii::t('frontend','Finalizing the plan'),
  'Once you choose a time and place, you can <strong>Complete</strong> the plan. We\'ll email the invitations and setup reminders.'
],
[
  '#tourDiscussion top',
  Yii::t('frontend','Share messages with participants '),
  'You can write back and forth with participants on the <strong>Messages</strong> tab. <p>Messages are delivered via email.</p>'
],
[
  '.container ',
  Yii::t('frontend','Ask a question'),
  'Need help?  and we\'ll respond as quickly as we can. <p>If you prefer, you can  in settings.</p>'
],
];
?>
<div id="tourButtons" class="hidden"><?= json_encode($tourButtons); ?></div>
<div id="tour" class="hidden"><?php echo Html::encode(json_encode($tour)); ?></div>
