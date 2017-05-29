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
            Reminder of Your Meeting
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            <?php
              echo Yii::t('frontend','Just a reminder about your upcoming meeting ').$display_time;
            // this code is similar to code in finalize-html
            if ($chosen_place!==false) {
            ?>
            &nbsp;at <?= Html::a($chosen_place->place->name,$chosen_place->place->website); ?>&nbsp;
              (<?php echo $chosen_place->place->vicinity; ?>, <?php echo HTML::a(Yii::t('frontend','view map'),$links['view_map']); ?> <?= Yii::t('frontend','or'); ?> <?= Html::a(Yii::t('frontend','directions to here'),Url::to('https://www.google.com/maps/dir//'.$chosen_place->place->full_address)); ?>)
              <?php
            } else {
            ?>
            &nbsp;via phone or video conference.
            <?php
              }
            ?>
            <br />
            Click below to view more details to view the meeting page.
          </td>
        </tr>
      <tr>
        <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:30px 0 30px 0" align="center">
          <div>
<!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo $links['view']; ?>" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
              <w:anchorlock/>
              <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
            </v:roundrect>
          <![endif]--><a href="<?php echo $links['view'] ?>" style='color:#fff; text-decoration:none; -webkit-text-size-adjust:none; background-color:#ff6f6f; border-radius:5px; display:inline-block; font-family:"Cabin", Helvetica, Arial, sans-serif; font-size:14px; font-weight:regular; line-height:45px; mso-hide:all; text-align:center; width:155px' bgcolor="#ff6f6f" align="center" width="155"><?= Yii::t('frontend','Visit Meeting Page'); ?></a>
</div>
        </td>
      </tr>
      <tr>
        <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:8px 20px; width:280px" align="center" width="280">
          <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate">
            <tr>
              <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; background-color:#fff; border:1px solid #ccc; border-radius:5px; padding:60px 75px; width:498px" align="center" bgcolor="#ffffff" width="498">
                <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                  <tr>
                    <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse" align="left">
                      <strong>Helpful options:</strong>
                      <p>
                        <?php
                          if ($showRunningLate) {
                              echo HTML::a(Yii::t('frontend','Inform them I\'m running late.'),$links['running_late']);
                          }
                        ?>
                      </p>
                        <?php if ($contacts_html <>'') {
                        ?>
                        <strong><?= Yii::t('frontend','Contacts:'); ?></strong>
                        <p>
                          <?= Meeting::buildContactListHtml($contactListObj); ?>
                       </p>
                      <?php }
                      ?>
                      <p>
                        <?php echo HTML::a(Yii::t('frontend','Adjust your reminder settings'),$links['reminders']); ?>
                      </p>
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
