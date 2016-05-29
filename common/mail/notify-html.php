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
            Changes to Your Meeting
          </td>
        </tr>
        <tr>
          <td class="free-text">
            <p>Hi <?php echo Html::encode(MiscHelpers::getDisplayName($user_id)); ?>, changes have been made to your meeting.</p>
            <p><?php echo $history; ?></p>
            <p>Please click the button below to view the meeting page.</p>
          </td>
        </tr>
        <tr>
          <td class="button">
            <div><!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
              </v:roundrect>
            <![endif]--><a class="button-mobile" href="<?php echo $links['view'] ?>"
            style="background-color:#ff6f6f;border-radius:5px;color:#ffffff;display:inline-block;font-family:'Cabin', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">Visit Meeting Page</a></div>
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
