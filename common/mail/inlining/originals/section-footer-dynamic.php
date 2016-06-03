<?php
use yii\helpers\Html;
use yii\helpers\Url;
use common\components\MiscHelpers;
?>
<tr>
  <td align="center" valign="top" width="100%" style="background-color: #f7f7f7; height: 100px;">
    <center>
      <table cellspacing="0" cellpadding="0" width="600" class="w320">
        <tr>
          <td style="padding: 25px 0 15px">
            <strong><?php echo Html::a(Yii::t('frontend','Meeting Planner'), $links['home']); ?></strong><br />
            Seattle, Washington<br />
          </td>
        </tr>
        <tr><td style="font-size:75%;"><em>
          <?php echo HTML::a(Yii::t('frontend','Email settings'),$links['footer_email']); ?>
          | <?php echo HTML::a(Yii::t('frontend','Block sender'),$links['footer_block']); ?>
          <?php //echo HTML::a(Yii::t('frontend','Block all'),$links['footer_block_all']); ?>
        </em>
        </td></tr>
      </table>
    </center>
  </td>
</tr>
