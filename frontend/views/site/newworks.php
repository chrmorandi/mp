<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="row firstpanel">
  <div class="col-xs-12 col-md-12 col-lg-12 text-center ">
    <h1><?=Yii::t('frontend','How Scheduling Works');?></h1>
  </div>
</div>
<!-- row 1 invite people -->
<div class="howitworks">
  <div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/people.gif'), ['class'=>'img-responsive hiw-visual-first']) ?>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <h3><?=Yii::t('frontend','Invite participants');?></h3>
      <p><?=Yii::t('frontend','Just one person or groups. You can also share the invitation link via email or Facebook.');?></p>
    </div>
  </div>
  <!-- row 2 suggest dates and times -->
  <div class="row">
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-first">
      <h3><?=Yii::t('frontend','Suggest dates and times');?></h3>
      <p><?=Yii::t('frontend','Just one person or groups. You can also share the invitation link via email or Facebook.');?></p>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/calendar.gif'), ['class'=>'img-responsive hiw-visual-second']) ?>
    </div>
  </div>
  <!-- row 3 suggest places -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/place.gif'), ['class'=>'img-responsive hiw-visual-first']) ?>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <h3><?=Yii::t('frontend','Suggest places');?></h3>
      <p><?=Yii::t('frontend','Adding one or more places.');?></p>
    </div>
  </div>
  <!-- row 4 - share availability -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-first">
      <h3><?=Yii::t('frontend','Sharing availability');?></h3>
      <p><?=Yii::t('frontend','Everyone shares their availability and preferences for each date time and place');?></p>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/availability.gif'), ['class'=>'img-responsive hiw-visual-second']) ?>
    </div>
  </div>

  <!-- row 5 choosing date time and place -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/choose.gif'), ['class'=>'img-responsive hiw-visual-first']) ?>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <h3><?=Yii::t('frontend','Choosing the Time and Place');?></h3>
      <p><?=Yii::t('frontend','Organizers choose the final time and place.');?></p>
    </div>
  </div>
  <!-- row 6 export to calendar -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-first">
      <h3><?=Yii::t('frontend','Add to Calendar');?></h3>
      <p><?=Yii::t('frontend','Exporting the event to your calendar is fast and easy.');?></p>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/exportcal.gif'), ['class'=>'img-responsive hiw-visual-second']) ?>
    </div>
  </div>
  <!-- row 7 - reminders-->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <?= Html::img(Url::to('/mp/img/home/reminder.gif'), ['class'=>'img-responsive hiw-visual-first']) ?>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <h3><?=Yii::t('frontend','Reminders');?></h3>
      <p><?=Yii::t('frontend','As the event nears, participants receive regular reminders via email.');?></p>
    </div>
  </div>
</div>
