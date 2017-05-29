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

            <?php
              $meeting_label=($is_activity==Meeting::IS_ACTIVITY?'Activity':'Meeting');
              if (!$reopened) {
                  echo Yii::t('frontend','Your '.$meeting_label.' is Planned!');
              } else {
                  echo Yii::t('frontend','Your '.$meeting_label.' Has Been Modified!');
              }
              ?>
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:10px 60px 0; width:100%" align="center" width="100%">
            <p><em>Hi, <?=  $owner; ?> has scheduled a <?= strtolower($meeting_label) ?> <?= $participantList ?> via <?php echo HTML::a(Yii::$app->params['site']['title'],MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_HOME,0,$user_id,$auth_key,$site_id)); ?>.</em></p>
            <p><?php echo $intro; ?></p>
            <p>Add this event to your calendar by opening the attachment below or <?php echo HTML::a(Yii::t('frontend','download it here'),$links['download']); ?>.</p>
          </td>
        </tr>
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:30px 0 30px 0" align="center">
            <div>
<!--[if mso]>
              <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo $links['view']; ?>" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#ff6f6f">
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
        <?php if ($is_activity==Meeting::IS_ACTIVITY) {?>
          <tr>
            <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:8px 20px; width:280px" align="center" width="280">
              <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate">
                <tr>
                  <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; background-color:#fff; border:1px solid #ccc; border-radius:5px; padding:10px 0 10px 15px; width:498px" align="center" bgcolor="#ffffff" width="498">
                    <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                      <tr>
                        <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse" align="left">
                          <span style="color:#4d4d4d; font-size:18px; font-weight:700; line-height:1.3; padding:5px 0">Activity</span><br>
                          <?= $chosenActivity->activity ?>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        <?php } ?>
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
                              <span style="color:#4d4d4d; font-size:18px; font-weight:700; line-height:1.3; padding:5px 0">Where</span><br />
                              <?php
                              if (!$noPlaces) {
                              ?>
                              <p><?= Html::a($chosenPlace->place->name.' ('.Yii::t('frontend','website').')', $chosenPlace->place->website); ?><br/ >
                                  <span style="font-size:75%;">
                                    <?php if (!empty($place->vicinity)) {
                                      ?>
                                  <?= $chosenPlace->place->vicinity; ?><br />
                                  <?php
                                }
                                ?>
                                <?php if (!empty($place->full_address)) {
                                  ?>
                                  <?php echo HTML::a(Yii::t('frontend','view map'),
                                  MiscHelpers::buildCommand($meeting_id,Meeting::COMMAND_VIEW_MAP,$chosenPlace->place->id,$user_id,$auth_key,$site_id)); ?>,
                                  <?= Html::a(Yii::t('frontend','directions'),Url::to('https://www.google.com/maps/dir//'.$chosenPlace->place->full_address)); ?>
                                  <?php
                                }
                                ?>
                                </span></p>
                                <?php
                              } else {
                                  ?>
                                  <?= Meeting::buildContactListHtml($contactListObj); ?>
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
