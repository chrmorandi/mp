<?php
use yii\helpers\Html;
?>
<?php
  if (count($notes)>0) {
?>
<tr>
      <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; padding:8px 20px; width:280px" align="center" width="280">
        <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse:separate">
          <tr>
            <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:center; border-collapse:collapse; background-color:#fff; border:1px solid #ccc; border-radius:5px; padding:60px 75px; width:498px" align="center" bgcolor="#ffffff" width="498">
              <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                <tr>
                  <td style="color:#777; font-family:Helvetica, Arial, sans-serif; font-size:14px; line-height:21px; text-align:left; border-collapse:collapse; padding-bottom:30px" align="left">
                    <strong>Notes:</strong>
                    <?php
                      foreach($notes as $n) {
                        ?>
                            <p><em><?php echo $n->postedBy->email; ?> says: </em>
                            "<?php echo $n->note; ?>"</p>
                        <?php
                          }
                          ?>
                        <?php echo HTML::a(Yii::t('frontend','add a note'),$links['addnote']); ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <?php
  }
?>
