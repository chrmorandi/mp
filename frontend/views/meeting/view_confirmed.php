<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\Marker;

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
          <p style="font-size:10px;"><em>All of this finalized meeting view is preliminary.</em></p>
        </div>
      </div>
    </div>
    <div class="panel-body">
    <?php echo $model->message.'&nbsp;';
    //echo Html::a(Yii::t('frontend','Download to Calendar'), ['download', 'id' => $model->id]);
    ?>
    </div>
    <div class="panel-footer">
      <div class="row">
        <div class="col-lg-6"></div>
        <div class="col-lg-6" >
          <div style="float:right;">
            <!--  to do - check meeting settings if participant can send/finalize -->
            <?php
            echo Html::a(Yii::t('frontend', 'Reschedule'), ['reschedule', 'id' => $model->id], ['id'=>'actionReschedule','class' => 'btn btn-default',
            'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);
            ?>
            <?php
            echo Html::a(Yii::t('frontend', 'Running Late'), ['late', 'id' => $model->id], ['id'=>'actionLate','class' => 'btn btn-default',
          'data-confirm' => Yii::t('frontend', 'Sorry, this feature is not yet available.')]);
            ?>
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
     echo $this->render('../participant/_panel', [
         'model'=>$model,
         'participantProvider' => $participantProvider,
     ]);
   }
    ?>

    <?php
      if (($model->meeting_type == \frontend\models\Meeting::TYPE_PHONE || $model->meeting_type == \frontend\models\Meeting::TYPE_VIDEO)) {
        // show conference contact info
        echo '<p>skype or phone contact info will appear here for your video or phone conference</p>';
      } else {
        // show place
        // show map
?>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading">
    <div class="row">
      <div class="col-lg-12"><h4>Where</h4></div>
    </div>
  </div>
  <div class="panel-body">
    <div class="col-lg-6">
    <div class="place-view">

        <p><?php echo $place->name; ?></p>
        <p><?php echo Html::a($place->website, $place->website); ?></p>
        <p><?php echo $place->full_address; ?></p>

    </div>
    </div> <!-- end first col -->
    <div class="col-lg-6">
      <?php
      if ($gps!==false) {
        $coord = new LatLng(['lat' => $gps->lat, 'lng' => $gps->lng]);
        $map = new Map([
            'center' => $coord,
            'zoom' => 14,
            'width'=>300,
            'height'=>300,
        ]);
        $marker = new Marker([
            'position' => $coord,
            'title' => $place->name,
        ]);
        // Add marker to the map
        $map->addOverlay($marker);
        echo $map->display();
      } else {
        echo 'No location coordinates for this place could be found.';
      }
      ?>

    </div> <!-- end second col -->
  </div>

</div>
<?php

      }
       ?>
       <div class="panel panel-default">
         <!-- Default panel contents -->
         <div class="panel-heading">
           <div class="row">
             <div class="col-lg-9"><h4><?= Yii::t('frontend','When') ?></h4><p><em>
             </div>
           </div>
         </div>
           <div class="panel-body">
             <p><?php echo $time; ?></p>
             
           </div>
         </div>
       </div>

    <?php echo $this->render('../meeting-note/_panel', [
            'model'=>$model,
            'noteProvider' => $noteProvider,
        ]) ?>

</div>
