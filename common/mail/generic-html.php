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
  <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; padding:20px 0 30px; background-color:#f7f7f7" bgcolor="#f7f7f7">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" style="border-collapse:collapse">
        <tr>
          <td style="color:#4d4d4d; font-family:Helvetica, Arial, sans-serif; font-size:32px; line-height:normal; text-align:center; border-collapse:collapse; font-weight:700; padding:35px 0 0" align="center">
            <?= Html::encode($content['heading']); ?>
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            <p>Hi <?php echo Html::encode(MiscHelpers::getDisplayName($user_id)); ?>,</p>
            <p><?= Html::encode($content['p1']) ?></p>
            <?php if ($content['p2']<>'') {
            ?>
            <p><?= Html::encode($content['p2']); ?></p>
            <?php
              }
            ?>
            <?php if (isset($content['p3']) && $content['p3']<>'') {
            ?>
            <p><?= Html::encode($content['p3']); ?></p>
            <?php
              }
            ?>
          </td>
        </tr>
        <?php if ($links['button_url']!='') {
         ?>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:30px 0 30px 0" align="center">
            <div>
<!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<a href="<?php echo $links['button_url'] ?>" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
              </v:roundrect>
            <![endif]--><a href="<?php echo $links['button_url'] ?>" style='color:#fff; text-decoration:none; -webkit-text-size-adjust:none; background-color:#ff6f6f; border-radius:5px; display:inline-block; font-family:"Cabin", Helvetica, Arial, sans-serif; font-size:14px; font-weight:regular; line-height:45px; mso-hide:all; text-align:center; width:155px' bgcolor="#ff6f6f" align="center" width="155"><?= Html::encode($content['button_text']); ?></a>
          </div>
          </td>
        </tr>
        <?php } ?>
      </table>
    </center>
  </td>
</tr>
<tr>
  <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; background-color:#fff; border-bottom:1px solid #e5e5e5; border-top:1px solid #e5e5e5" bgcolor="#ffffff">
    <center>
      <br>
    </center>
  </td>
</tr>
<?php echo \Yii::$app->view->renderFile('@common/mail/section-footer-dynamic.php',['links'=>$links]) ?>
