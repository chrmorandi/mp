<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\BaseHtml;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use frontend\models\Meeting;
use common\components\MiscHelpers;
use dosamigos\google\maps\Map;
use dosamigos\google\maps\LatLng;
use dosamigos\google\maps\overlays\Marker;
use frontend\assets\MeetingAsset;
MeetingAsset::register($this);

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
          Yii::$app->getSession()->setFlash('info', Yii::t('frontend','This meeting has passed.'));
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
        'participant'=>$participant,
        'friends'=>$friends,
    ]);
   ?>
   <?php  // what
   echo $this->render('./_panel_what', [
       'model'=>$model,
       'isOwner' => $isOwner,
   ]) ?>

    <div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading" role="tab" id="headingWhen">
        <div class="row">
          <div class="col-lg-9"><h4><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhen" aria-expanded="true" aria-controls="collapseWhen"><?= Yii::t('frontend','When') ?></a></h4><p><em>
          </div>
        </div>
      </div>
        <div id="collapseWhen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhen">
          <div class="panel-body">
            <p><?php echo $time; ?></p>
          </div>
        </div>
      </div>

    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingWhere">
        <div class="row">
          <div class="col-lg-12"><h4><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseWhere" aria-expanded="true" aria-controls="collapseWhere"><?= Yii::t('frontend','Where') ?></a></h4></div>
        </div>
      </div>
      <div id="collapseWhere" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingWhere">
        <div class="panel-body">
          <?php
            if ($noPlace) { ?>
              <div class="col-lg-12">
                <?= '<strong>'.Yii::t('frontend','Virtual meeting').'</strong><br /><br />';?>
                <?= Meeting::buildContactListHtml($contactListObj); ?>
            </div>
      <?php
          } else {
            // show place and map
            ?>
<?php if (empty(!$place)) {
  ?>
  <div class="col-lg-6">
        <div class="place-view">
        <p><?= Html::a($place->name.' ('.$place->website.')', $place->website); ?></p>
        <p><?= $place->vicinity; ?><br />
        <p><?= Html::a(Yii::t('frontend','view map'),Url::to('https://www.google.com/maps/place/'.$place->full_address)); ?>,
        <?= Html::a(Yii::t('frontend','directions to here'),Url::to('https://www.google.com/maps/dir//'.$place->full_address)); ?></p>
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
  </div>
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
<?= BaseHtml::hiddenInput('url_prefix',MiscHelpers::getUrlPrefix(),['id'=>'url_prefix']); ?>
<?= BaseHtml::hiddenInput('tz_dynamic','',['id'=>'tz_dynamic']); ?>
<?= BaseHtml::hiddenInput('tz_current',$timezone,['id'=>'tz_current']); ?>
