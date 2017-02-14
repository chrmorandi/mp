<?php
use common\components\MiscHelpers;
?>
<tr >
  <td >
      <div>
        <?= $model->note ?>
      </div>
      <div class="emright">
        <?= Yii::t('frontend','By ').MiscHelpers::getDisplayName($model->postedBy->id) ?>
      </div>
    </td>
</tr>
