<tr >
  <td >
      <div>
        <?= $model->note ?>
      </div>
      <div class="emright">
        <?= Yii::t('frontend','By ').$model->postedBy->email ?>
      </div>
</td>
</tr>
