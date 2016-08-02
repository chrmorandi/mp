<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingNote;
use frontend\models\MeetingPlace;
use frontend\models\MeetingTime;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
?>
<tr>
  <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; padding:20px 0 30px; background-color:#f7f7f7" bgcolor="#f7f7f7">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" style="border-collapse:collapse">
        <tr>
          <td style="color:#4d4d4d; font-family:Helvetica, Arial, sans-serif; font-size:32px; line-height:normal; text-align:center; border-collapse:collapse; font-weight:700; padding:35px 0 0" align="center">
            Your Meeting is Planned!
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            <p><em>Hi, <?php echo $owner; ?> is inviting you to an event using a new service called <?php echo HTML::a(Yii::t('frontend','Meeting Planner'),MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_HOME,0,$user_id,$auth_key)); ?>. The service makes it easy to plan meetings without the exhausting threads of repetitive emails. Please try it out below.</em></p>
            <p><?php echo $intro; ?></p>
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:30px 0 30px 0" align="center">
            <div>
<!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
                <w:anchorlock/>
                <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">Track Order</center>
              </v:roundrect>
            <![endif]--><a href="<?php echo $links['view']; ?>" style='color:#fff; text-decoration:none; -webkit-text-size-adjust:none; background-color:#ff6f6f; border-radius:5px; display:inline-block; font-family:"Cabin", Helvetica, Arial, sans-serif; font-size:14px; font-weight:regular; line-height:45px; mso-hide:all; text-align:center; width:155px' bgcolor="#ff6f6f" align="center" width="155">View Meeting Plan</a>
</div>
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            Alternately, you can <?php echo HTML::a(Yii::t('frontend','cancel the meeting'),$links['cancel']); ?>.
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse" align="center">
            <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
              <tr>
                <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 0 10px 15px; vertical-align:top; width:278px" align="center" valign="top" width="278">
                  <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                    <tr>
                      <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse" align="center">
                        <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate">
                          <tr>
                            <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse; background-color:#fff; border:1px solid #e5e5e5; border-radius:5px; padding:12px 15px 15px; vertical-align:top; width:253px" align="left" bgcolor="#ffffff" valign="top" width="253">
                              <span style="color:#4d4d4d; font-size:18px; font-weight:700; line-height:1.3; padding:5px 0">When</span><br>
                              <?php echo Meeting::friendlyDateFromTimestamp($chosenTime->start); ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
                <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 14px 10px 15px; vertical-align:top; width:278px" align="center" valign="top" width="278">
                  <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                    <tr>
                      <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse" align="center">
                        <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate">
                          <tr>
                            <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse; background-color:#fff; border:1px solid #e5e5e5; border-radius:5px; padding:12px 15px 15px; vertical-align:top; width:253px" align="left" bgcolor="#ffffff" valign="top" width="253">
                              <span class="header-sm">Where</span><br />
                              <?php
                              if (!$noPlaces) {
                              ?>
                                <?php echo $chosenPlace->place->name; ?>
                                <br/ >
                                <span style="font-size:75%;"><?php echo $chosenPlace->place->vicinity; ?> <?php echo HTML::a(Yii::t('frontend','view map'),
                                MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_VIEW_MAP,$chosenPlace->place->id,$user_id,$auth_key)); ?></span>
                                <?php
                              } else {
                                  ?>
                                  Phone or video <br />
                                  Contact info will appear here in the future<br />
                              <?php
                                }
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
          </td>
        </tr>
        <?php echo \Yii::$app->view->renderFile('@common/mail/section-notes.php',['notes'=>$notes,'links'=>$links]) ?>
      </table>
    </center>
  </td>
</tr>
<?php echo \Yii::$app->view->renderFile('@common/mail/section-footer-dynamic.php',['links'=>$links]) ?>
