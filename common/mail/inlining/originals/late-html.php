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
            Late Notice
          </td>
        </tr>
        <tr>
          <td class="free-text">
            Just a note that <?php echo $sender_name; ?> is running a few minutes late for your meeting.<br />
            Click below to view the meeting page.
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
      <tr>
        <td class="mini-large-block-container">
          <table cellspacing="0" cellpadding="0" width="100%"  style="border-collapse:separate !important;">
            <tr>
              <td class="mini-large-block">
                <table cellpadding="0" cellspacing="0" width="100%">
                  <tr>
                    <td style="text-align:left;">
                      <strong>Helpful options:</strong>
                      <p>
                        <?php
                          echo HTML::a(Yii::t('frontend','Inform them I\'m running late as well.'),$links['running_late']);
                        ?>
                      </p>
                        <?php if ($contacts_html <>'') {
                        ?>
                        <strong>Contact details:</strong>
                        <p>
                        <?php
                          echo $contacts_html;
                         ?>
                       </p>
                      <?php }
                      ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
  </td>
</tr>
<?php echo \Yii::$app->view->renderFile('@common/mail/section-footer-dynamic.php',['links'=>$links]) ?>
