<?php
use yii\helpers\Html;
?>

<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-9"><h4>What</h4></div>
      <div class="col-lg-3" ><div style="float:right;">
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
