<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $user common\models\User */
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<tr>
  <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; padding:20px 0 30px; background-color:#f7f7f7" bgcolor="#f7f7f7">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" style="border-collapse:collapse">
        <tr>
          <td style="color:#4d4d4d; font-family:Helvetica, Arial, sans-serif; font-size:32px; line-height:normal; text-align:center; border-collapse:collapse; font-weight:700; padding:35px 0 0" align="center">
            Reset Your Password
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            Hello <?php echo Html::encode(\common\components\MiscHelpers::getDisplayName($user->id)); ?>,
            Click the button below to reset your Meeting Planner password:
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:30px 0 30px 0" align="center">
            <div>
<!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
              </v:roundrect>
            <![endif]--><a href="<?php echo $resetLink ?>" style='color:#fff; text-decoration:none; -webkit-text-size-adjust:none; background-color:#ff6f6f; border-radius:5px; display:inline-block; font-family:"Cabin", Helvetica, Arial, sans-serif; font-size:14px; font-weight:regular; line-height:45px; mso-hide:all; text-align:center; width:155px' bgcolor="#ff6f6f" align="center" width="155">Reset Your Password</a>
</div>
          </td>
        </tr>
      </table>
    </center>
  </td>
</tr>
<?php echo \Yii::$app->view->renderFile('@common/mail/section-footer-static.php') ?>
