<?php
use yii\helpers\Html;
?>
<?php
  if (count($notes)>0) {
?>
    <tr>
      <td class="mini-large-block-container">
        <table cellspacing="0" cellpadding="0" width="100%"  style="border-collapse:separate !important;">
          <tr>
            <td class="mini-large-block">
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td style="text-align:left; padding-bottom: 30px;">
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
