<?php
use yii\helpers\Html;
?>
<tr>
  <td class="free-text">
    <?php
      if (count($notes)>0) {
        ?>
          <strong>Notes:</strong><br />
                <?php echo HTML::a(Yii::t('frontend','add a note'),$links['addnote']); ?> <br />
    <?php
      foreach($notes as $n) {
        ?>
            <p><em><?php echo $n->postedBy->email; ?> says: </em>
            "<?php echo $n->note; ?>"
          </p><br/ >
            <?php
          }
      ?>
      <?php
      }
      ?>
  </td>
</tr>
