<?php
use yii\helpers\Html;
?>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-4 col-md-4 col-xs-4"><h4 class="meeting-view">What</h4>
        <span class="hint-text"><?= Yii::t('frontend','edit the subject of your meeting') ?></span>
      </div>
      <div class="col-lg-8 col-md-8 col-xs-8"><div style="float:right;">
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
    if ($model->has_subject) {
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
