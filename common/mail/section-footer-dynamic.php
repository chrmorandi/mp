<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
?>
<tr>
  <td align="center" valign="top" width="100%" style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:18px; text-align:center; background-color:#f7f7f7; height:100px" bgcolor="#f7f7f7" height="100">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" style="border-collapse:collapse">
        <tr>
          <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; padding:25px 0 15px" align="center">
            <strong><?php echo Html::a(Yii::$app->params['site']['title'], $links['home']); ?></strong><br />
<em>
  <?php
    if (isset($mode)) {
      if ($mode == 'update') {
         echo HTML::a(Yii::t('frontend','Email settings'),$links['footer_email'], ['style' => 'font-size:75%;']);
         echo ' | ';
         echo HTML::a(Yii::t('frontend','Block updates'),$links['footer_block_updates'], ['style' => 'font-size:75%;']);
         ?>
    <?php
      }
    } else {
      ?>
      <?php echo HTML::a(Yii::t('frontend','Email settings'),$links['footer_email'], ['style' => 'font-size:75%;']); ?>
      <?php
        if (isset($links['footer_block'])) {
      ?>
      | <?php echo HTML::a(Yii::t('frontend','Block sender'),$links['footer_block'], ['style' => 'font-size:75%;']); ?>
      <?php
        }
       ?>
      <?php //echo HTML::a(Yii::t('frontend','Block all'),$links['footer_block_all']); ?>
      <?php
    }
  ?>
          </em>
        </td></tr>
      </table>
    </center>
  </td>
</tr>
