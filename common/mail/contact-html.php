<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\UserContact;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
?>
<tr>
  <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;" class="content-padding">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" class="w320">
        <tr>
          <td class="header-lg">
            Share Your Contact Information
          </td>
        </tr>
        <tr>
          <td class="free-text">
            Hi <?php echo Html::encode(MiscHelpers::getDisplayName($user_id)); ?>,
            We don't have any contact information for you for your <?php echo HTML::a(Yii::t('frontend','upcoming meeting'),$links['view']); ?>.
            Please click the button below to add your phone number or online conferencing details.
          </td>
        </tr>
        <tr>
          <td class="button">
            <div><!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
              </v:roundrect>
            <![endif]--><a class="button-mobile" href="<?php echo $links['add_contact'] ?>"
            style="background-color:#ff6f6f;border-radius:5px;color:#ffffff;display:inline-block;font-family:'Cabin', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:175px;-webkit-text-size-adjust:none;mso-hide:all;">Add Contact Information</a></div>
          </td>
        </tr>
      </table>
    </center>
  </td>
</tr>
<tr>
  <td align="center" valign="top" width="100%" style="background-color: #ffffff;  border-top: 1px solid #e5e5e5; border-bottom: 1px solid #e5e5e5;">
    <center>
      <br />
    </center>
  </td>
</tr>
<?php echo \Yii::$app->view->renderFile('@common/mail/section-footer-dynamic.php',['links'=>$links]) ?>
