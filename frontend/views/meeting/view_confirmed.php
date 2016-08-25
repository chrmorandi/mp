<?php

use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use common\components\MiscHelpers;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\Marker;

/* @var $this yii\web\View */
/* @var $model frontend\models\Meeting */

$this->title = $model->getMeetingHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meetings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo $this->render('_timezone_alerts');
?>
<div class="meeting-view">

  <?php
    if ( $model->status >= $model::STATUS_COMPLETED) {
      switch ($model->status) {
        case $model::STATUS_EXPIRED:
          Yii::$app->getSession()->setFlash('warning', Yii::t('frontend','This meeting expired due to inactivity.'));
        break;
        case $model::STATUS_COMPLETED:
          Yii::$app->getSession()->setFlash('info', Yii::t('frontend','This meeting has past.'));
        break;
        case $model::STATUS_CANCELED:
          Yii::$app->getSession()->setFlash('warning', Yii::t('frontend','This meeting was canceled.'));
        break;
        case $model::STATUS_TRASH:
          Yii::$app->getSession()->setFlash('danger', Yii::t('frontend','This meeting has been deleted.'));
        break;
      }
      echo $this->render('_command_bar_past', [
          'model'=>$model,
          'isPast'=>true,
          'dropclass'=>'dropdown',
          'isOwner' => $isOwner,
      ]);
    } else {
      echo $this->render('_command_bar_confirmed', [
          'model'=>$model,
          'meetingSettings' => $meetingSettings,
          'showRunningLate'=>$showRunningLate,
          'isPast'=>$isPast,
          'dropclass'=>'dropdown',
          'isOwner' => $isOwner,
      ]);
    }
  ?>

  <?php
    echo $this->render('../participant/_panel', [
        'model'=>$model,
        'participantProvider' => $participantProvider,
    ]);
   ?>

   <div class="panel panel-default">
     <!-- Default panel contents -->
     <div class="panel-heading">
       <div class="row">
         <div class="col-lg-9"><h4>What</h4></div>
         <div class="col-lg-3" ><div style="float:right;">

         </div>
       </div>
       </div>
     </div>
     <div class="panel-body">
       <?php echo Html::encode($this->title) ?>
     <?php echo $model->message.'&nbsp;'; ?>
     </div>
   </div>


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

    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-lg-12"><h4>Where</h4></div>
        </div>
      </div>
      <div class="panel-body">
    <?php
      if ($noPlace) { ?>
        <div class="col-lg-12">
      <?php
        // show conference contact info
        if (count($contacts)>0) {
          foreach ($contacts as $c) {

          ?>
          <p>
          <?php
            echo $contactTypes[$c['contact_type']].': '.$c['info'];
          ?>
        </p>
        <?php
          }
        } else {
          echo '<p>'.Yii::t('frontend','No contact information available for your meeting partner yet.').'</p>';
        }
        ?>
  </div>
  <?php
      } else {
        // show place and map
?>
<?php if (empty(!$place)) {
  ?>

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
    <?php
    }
  ?>
    <?php
  }
   ?>
  </div> <!-- end panel body -->
</div> <!-- end panel -->
    <?php echo $this->render('../meeting-note/_panel', [
            'model'=>$model,
            'noteProvider' => $noteProvider,
        ]) ?>
        <?php
          if ( $model->status >= $model::STATUS_COMPLETED) {
            echo $this->render('_command_bar_past', [
                'model'=>$model,
                'isPast'=>true,
                'dropclass'=>'dropup',
                'isOwner' => $isOwner,
            ]);
          } else {
            echo $this->render('_command_bar_confirmed', [
                'model'=>$model,
                'meetingSettings' => $meetingSettings,
                'showRunningLate'=>$showRunningLate,
                'isPast'=>$isPast,
                'dropclass'=>'dropup',
                'isOwner' => $isOwner,
            ]);
          }
        ?>

</div> <!-- end meeting view -->
<?php
  $this->registerJsFile(MiscHelpers::buildUrl().'/js/jstz.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
  $this->registerJsFile(MiscHelpers::buildUrl().'/js/meeting.js',['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?= BaseHtml::hiddenInput('url_prefix',MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
<?= BaseHtml::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= BaseHtml::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
