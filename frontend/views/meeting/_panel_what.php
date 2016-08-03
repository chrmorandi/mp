<?php
use yii\helpers\Html;
?>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-10 col-md-10 col-xs-10"><h4 class="meeting-view">What</h4>
        <span class="hint-text"><?= Yii::t('frontend','edit the subject for your meeting') ?></span>
      </div>
      <div class="col-lg-2 col-md-2 col-xs-2" ><div style="float:right;">
      <?php
        if ($isOwner) {
            echo Html::a('', ['update', 'id' => $model->id], ['class' => 'btn btn-primary glyphicon glyphicon-pencil','title'=>'Edit']);
          }
        ?>
      </div>
    </div>
    </div>
  </div>
  <?php
    if ($model->has_subject || $model->subject == \frontend\models\Meeting::DEFAULT_SUBJECT) {
      ?>
      <div class="panel-body">
        <?php echo Html::encode($this->title) ?>
      <?php echo $model->message.'&nbsp;'; ?>
      </div>
      <?php
    } else {
      ?>
      <?php
        }
        ?>
</div>
