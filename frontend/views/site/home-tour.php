<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="home-tour">
  <div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12 text-center firstpanel">
      <h1><?=Yii::t('frontend','How planning works');?></h1>
    </div>
  </div>
  <!-- row 1 invite people -->
  <div class="row">
    <div class="col-xs-12 col-md-6 ">
      <div class="row1-left pull-right" >
        <?= Html::img(Url::to('/mp/img/home/people.gif'), ['class'=>'img-responsive'])//  hiw-visual-first ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 hiw-text-second">
      <div class="row1-right pull-left" >
        <h3><?=Yii::t('frontend','Invite participants');?></h3>
        <p><?=Yii::t('frontend','Just one person or groups. You can also share the invitation link via email or Facebook.');?></p>
      </div>
    </div>
  </div>
  <!-- row 2 suggest dates and times -->
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-push-6">
      <div class="row2-right pull-left" >
        <?= Html::img(Url::to('/mp/img/home/calendar.gif'), ['class'=>'img-responsive'])// hiw-visual-second ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 hiw-text-first">
      <div class="row2-left pull-left" >
        <h3><?=Yii::t('frontend','Suggest dates and times');?></h3>
        <p><?=Yii::t('frontend','Just one person or groups. You can also share the invitation link via email or Facebook.');?></p>
      </div>
    </div>
  </div>
  <!-- row 3 suggest places -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row3-left pull-right" >
        <?= Html::img(Url::to('/mp/img/home/place.gif'), ['class'=>'img-responsive'])// hiw-visual-first ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <div class="row3-right pull-left" >
        <h3><?=Yii::t('frontend','Suggest places');?></h3>
        <p><?=Yii::t('frontend','Adding one or more places.');?></p>
      </div>
    </div>
  </div>
  <!-- row 4 - share availability -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 ">
      <div class="row4-right pull-left" >
        <?= Html::img(Url::to('/mp/img/home/availability.gif'), ['class'=>'img-responsive']) //  hiw-visual-second ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 col-lg-6 col-lg-pull-6  hiw-text-first">
      <div class="row4-left pull-left" >
        <h3><?=Yii::t('frontend','Sharing availability');?></h3>
        <p><?=Yii::t('frontend','Everyone shares their availability and preferences for each date time and place');?></p>
      </div>
    </div>
  </div>
  <!-- row 5 choosing date time and place -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row5-left pull-right" >
        <?= Html::img(Url::to('/mp/img/home/choose.gif'), ['class'=>'img-responsive ']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <div class="row5-right pull-left" >
        <h3><?=Yii::t('frontend','Choosing the Time and Place');?></h3>
        <p><?=Yii::t('frontend','Organizers choose the final time and place.');?></p>
      </div>
    </div>
  </div>
  <!-- row 6 export to calendar -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 ">
      <div class="row6-right pull-left" >
        <?= Html::img(Url::to('/mp/img/home/exportcal.gif'), ['class'=>'img-responsive']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 col-lg-6 col-lg-pull-6  hiw-text-first">
      <div class="row6-left pull-left" >
        <h3><?=Yii::t('frontend','Add to Calendar');?></h3>
        <p><?=Yii::t('frontend','Exporting the event to your calendar is fast and easy.');?></p>
      </div>
    </div>
  </div>
  <!-- row 7 - reminders-->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row7-left pull-right" >
        <?= Html::img(Url::to('/mp/img/home/reminder.gif'), ['class'=>'img-responsive hiw-visual-first']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <div class="row7-right pull-left" >
        <h3><?=Yii::t('frontend','Reminders');?></h3>
        <p><?=Yii::t('frontend','As the event nears, participants receive regular reminders via email.');?></p>
      </div>
    </div>
  </div>
</div>
