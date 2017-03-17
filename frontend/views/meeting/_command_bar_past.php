<?php
use yii\helpers\Html;
use frontend\models\Meeting;
use frontend\models\MeetingSetting;
?>

  <div class="command-bar">
    <div class="row">
      <div class="col-xs-3">
        <div class="<?= $dropclass ?>" >
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        <?= Yii::t('frontend','Options');?>
        <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <!-- <li role="separator" class="divider"></li>-->
        <li><?= Html::a(Yii::t('frontend', 'History'), ['/meeting-log/view', 'id' => $model->id],
         [
         'title'=>Yii::t('frontend','View the historical log of meeting adjustments'),
        ]); ?></li>
        </ul>
        </div>
      </div>
      <div class="col-xs-9" >
        <div style="float:right;">
          <span class="button-pad">
            <?= Html::a('<i class="glyphicon glyphicon-repeat"></i>&nbsp;'.Yii::t('frontend', 'Repeat'), ['repeat','id'=>$model->id],
             [
             'class' => 'btn btn-default btn-primary',
             'title'=>Yii::t('frontend','Repeat this meeting at a new time'),
             ]); ?>
          </span>
                  </div>
      </div>
    </div> <!-- end row -->
</div>
