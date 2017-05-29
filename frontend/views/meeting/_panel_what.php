<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use frontend\models\Meeting;
?>
<div class="panel panel-meeting" id="headingWhat">
  <!-- Default panel contents -->
  <div id="jumpActivity"></div>
  <div class="panel-heading" role="tab" >
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4  class="meeting-view"><?= Yii::t('frontend','Subject')?></h4>
        <!-- <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhat" aria-expanded="true" aria-controls="collapseWhat"></a> -->
        <?php
          if ($model->status >= Meeting::STATUS_CONFIRMED) {
            $hint = Yii::t('frontend','the subject of your {meeting}',['meeting'=>strtolower(Yii::t('frontend',Yii::$app->params['site']['mtg_singular']))]);
          } else {
            $hint = Yii::t('frontend','the reason for your {meeting}',['meeting'=>strtolower(Yii::t('frontend',Yii::$app->params['site']['mtg_singular']))]);
          }
        ?>
        <span class="hint-text"><?= $hint; ?></span>
      </div>
      <div class="col-lg-2 col-md-2 col-xs-2" ><div style="float:right;">
      <?php
        if ($model->isOrganizer() && $model->status <= Meeting::STATUS_CONFIRMED) {
          //['update', 'id' => $model->id]
            echo Html::a('', 'javascript:void(0);', ['class' => 'btn btn-primary glyphicon glyphicon-pencil','title'=>'Edit','onclick'=>'showWhat();']);
          }
        ?>
      </div>
    </div>
    </div>
  </div>
  <?php
    if ($model->has_subject || $model->subject == Yii::t('frontend','Our Upcoming Meeting') || $model->subject == Yii::t('frontend','Our Upcoming Meetup')) {
      ?>
      <div id="collapseWhat" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhat">
        <div class="panel-body">
          <div id="showWhat">
          <span>
          <?php if (empty($model->message)) {
            echo Html::encode($this->title);
            // note: required because couldn't prevent extra space
          } else {
            echo Html::encode($this->title).': '.Html::encode($model->message).'&nbsp;';
          } ?>
        </span>
          </div>
          <div id="editWhat" class="hidden">
            <?= $this->render('_form', [
                'model' => $model,
                'subjects' =>  $model->defaultSubjectList(),
            ]) ?>
          </div>
        </div>
      </div>
      <?php
    } else {
      ?>
      <?php
        }
        ?>
</div>
