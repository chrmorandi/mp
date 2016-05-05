<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
?>
<table  cellpadding="0" cellspacing="10" border="0" align="center" width="600">
  <tr>
    <td colspan="2">
      <p><em>Hi, this email is regarding our new service <?php echo HTML::a(Yii::t('frontend','Meeting Planner'),$links['home']); ?>. The service makes it easy to plan meetings without the exhausting threads of repetitive emails.</em></p>
      <p>We don't have any contact information for you for your <?php echo HTML::a(Yii::t('frontend','upcoming meeting'),$links['view']); ?>.
        Please return to the site and <?php echo HTML::a(Yii::t('frontend','add your phone number or online conferencing details'),$links['add_contact']); ?> so that we can share it with other participants.</p>
    </td>
  </tr>
</table>
<table  cellpadding="0" cellspacing="10" border="0" align="center" width="600">
  <tr><td width="300" style="text-align:center;margin:10px;">
<p>
  <?php echo Html::a(Yii::t('frontend','Visit Meeting Planner'), $links['home']); ?>
</p>
</td></tr>
<tr><td width="300" style="text-align:center;font-size:75%;margin:10px;">
  <em>
    <?php echo HTML::a(Yii::t('frontend','Review your email settings'),$links['footer_email']); ?>
    | <?php echo HTML::a(Yii::t('frontend','Block this person'),$links['footer_block']); ?>
    | <?php echo HTML::a(Yii::t('frontend','Block all emails'),$links['footer_block_all']); ?>
  </em>
</td></tr>
</table>
