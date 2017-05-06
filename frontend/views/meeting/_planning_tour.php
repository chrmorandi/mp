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
$args=[
  'mlabel'=>Yii::t('frontend','meeting'),
  'em1'=>'<em>',
  'em2'=>'</em>',
  'strong1'=>'<strong>',
  'strong2'=>'</strong>',
];
$tour=[
[
  '.nav-tabs top',
  Yii::t('frontend','Welcome'),
  Yii::t('frontend','Allow me to show you how to plan your {mlabel}.',$args).'<br /><br />'.Yii::t('frontend','If you prefer,').'&nbsp;'.Html::a(Yii::t('frontend','permanently turn off this guide').'.','javascript::return false;',['onclick'=>'turnOffGuide();'])],
[
  '#headingWho top',
  Yii::t('frontend','Who would you like to invite?'),
  Yii::t('frontend','You can add one person or a group of people to your {mlabel}.',$args)],
[
  '#invitation-url bottom',
  Yii::t('frontend','Sharing the link'),
  Yii::t('frontend','Or, you can share the {mlabel} link with people via email, websites and social media',$args),
],
[
  '#headingWhat bottom',
  Yii::t('frontend','What is your meeting about?'),
  Yii::t('frontend','You can customize the subject of your {mlabel} invitation',$args),
]
];
if ($model->is_activity == $model::IS_ACTIVITY) {
  $tour[]=[
    '#headingActivity top',
    Yii::t('frontend','What do you want to do?'),
    Yii::t('frontend','You can suggest activities. With multiple ideas, people can help you choose.'),
  ];
}
$tour[]=[
  '#headingWhen top',
  Yii::t('frontend','When do you want to meet?'),
  Yii::t('frontend','Suggest dates and times for your {mlabel}. With more than one, people can offer feedback and help you choose.',$args),
];
$tour[]=
[
  '#headingWhere top',
  Yii::t('frontend','Where do you want to meet?'),
  Yii::t('frontend','Suggest places for your {mlabel}. With multiple places, people can offer feedback and help you choose.',$args),
];
$tour[]=
[
  '#virtualThingBox top',
  Yii::t('frontend','Is this a virtual meeting?'),
  Yii::t('frontend','Switch between {em1}in person{em2} and {em1}virtual{em2} {mlabel}s such as phone calls or online conferences.',$args),
];
if ($model->isOrganizer() && $model->status < $model::STATUS_SENT)
 {
   // send button may not exist
$tour[]=[
  '#actionSend top',
  Yii::t('frontend','Sending invitations'),
  Yii::t('frontend','After you add times and places, you can {strong1}Invite{strong2} participants to select their favorites. {em1}A place isn\'t necessary for virtual {mlabel}s{em2}.',$args),
];
 }
 $tour[]=
[
  '#actionFinalize right',
  Yii::t('frontend','Finalizing the plan'),
  Yii::t('frontend','Once you choose the time and place, you can {strong1}Complete{strong2} the plan. We\'ll email the invitations and setup reminders.',$args),
];
$tour[]=
[
  '#tourDiscussion top',
  Yii::t('frontend','Share messages with participants '),
  Yii::t('frontend','You can write back and forth with participants on the {strong1}Messages{strong2} tab. We deliver messages via email.',$args),
];
$tour[]=
[
  '.container ',
  Yii::t('frontend','Ask a question'),
  Yii::t('frontend','Need help?').'&nbsp;'.Html::a(Yii::t('frontend','Ask a question'),['ticket/create']).'&nbsp;'.Yii::t('frontend','and we\'ll respond as quickly as we can.').'<br /><br />'.Yii::t('frontend','If you prefer,').'&nbsp;'.Html::a(Yii::t('frontend','permanently turn off this guide').'.',['user-setting/index','tab'=>'guide']),
];
?>
<div id="tourButtons" class="hidden"><?= json_encode($tourButtons); ?></div>
<div id="tour" class="hidden"><?php echo Html::encode(json_encode($tour)); ?></div>
