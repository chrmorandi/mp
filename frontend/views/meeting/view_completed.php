<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */

$this->title = $model->getMeetingHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="meeting-view">

  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading">
      <div class="row">
        <div class="col-lg-12"><h1><?php echo Html::encode($this->title) ?></h1>
          <p style="font-size:9px;"><em>All of this finalized meeting view is preliminary.</em></p>
        </div>
      </div>
    </div>
    <div class="panel-body">
    <?php echo $model->message.'&nbsp;';
    echo Html::a(Yii::t('frontend','Download to Calendar'), ['download', 'id' => $model->id]);
    ?>
    </div>
    <div class="panel-footer">
      <div class="row">
        <div class="col-lg-6"></div>
        <div class="col-lg-6" >
          <div style="float:right;">
            <!--  to do - check meeting settings if participant can send/finalize -->
          <?php echo Html::a('', ['cancel', 'id' => $model->id],
           ['class' => 'btn btn-primary glyphicon glyphicon-remove btn-danger',
           'title'=>Yii::t('frontend','Cancel'),
           'data-confirm' => Yii::t('frontend', 'Are you sure you want to cancel this meeting?')
           ]) ?>
          </div>
        </div>
    </div> <!-- end row -->
    </div>
   </div>
    <?php if ($isOwner) {
      // show participant
      echo '<p>you are the meeting participant</p>';
    } else {
      // show owner
      echo '<p>you are the meeting organizer</p>';
    }
     ?>
    <?php
      if (($model->meeting_type == \frontend\models\Meeting::TYPE_PHONE || $model->meeting_type == \frontend\models\Meeting::TYPE_VIDEO)) {
        // show conference contact info
        echo '<p>skype or phone contact info will appear here for your video or phone conference</p>';
      } else {
        // show place
        // show map
        echo '<p>the chosen place and a map will appear here</p>';
      }
       ?>

    <?php
      // show the date and time
      echo '<p>the chosen date and time will appear here</p>';
     ?>

     <?php
     echo '<p>command options such as cancel, reschedule etc will appear here</p>';
     // show the command bar header_remove
     // cancel
     // reschedule
     // pick a new place
     // pick a new time
     ?>

    <?php echo $this->render('../meeting-note/_panel', [
            'model'=>$model,
            'noteProvider' => $noteProvider,
        ]) ?>

</div>
