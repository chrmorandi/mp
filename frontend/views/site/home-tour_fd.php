<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<div class="home-tour">
  <div class="row">
    <div class="col-xs-12 col-md-12 col-lg-12 firstpanel">
      <div class="centered">
      <h1><?=Yii::t('frontend','How it works');?></h1>
    </div>
    </div>
  </div>
  <!-- row 1 invite people -->
  <div class="row">
    <div class="col-xs-12 col-md-6">
      <div class="row1-left" >
        <?= Html::img(Url::to('/img/home/people.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6">
      <div class="row1-right pull-left" >
        <h3><?=Yii::t('frontend','Provide participant emails');?></h3>
        <p><?=Yii::t('frontend','Type or paste in the email addresses of people you wish to invite. You can also share the invitation link via email or post on Facebook. When you\'re ready to send the invitation, we\'ll deliver it for you.',['site-title'=>Yii::$app->params['site']['title']]);?></p>
      </div>
    </div>
  </div>
  <!-- row 2 suggest dates and times -->
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-push-6">
      <div class="row2-right pull-left" >
        <?= Html::img(Url::to('/img/home/calendar.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 hiw-text-first">
      <div class="row2-left" >
        <h3><?=Yii::t('frontend','Suggest meeting times');?></h3>
        <p><?=Yii::t('frontend','No more emails about which times will work best. Suggest one or more times that work for your schedule in seconds and we\'ll ask participants about their availability.');?></p>
      </div>
    </div>
  </div>
  <!-- row 3 suggest places -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row3-left " >
        <?= Html::img(Url::to('/img/home/place.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <div class="row3-right pull-left" >
        <h3><?=Yii::t('frontend','Suggest meeting places');?></h3>
        <p><?=Yii::t('frontend','No more emails about which places might work. Suggest one or more via Google Places and we\'ll ask participants which they prefer. ');?></p>
      </div>
    </div>
  </div>
  <!-- row 4 - share availability -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 ">
      <div class="row4-right pull-left" >
        <?= Html::img(Url::to('/img/home/availability.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 col-lg-6 col-lg-pull-6  hiw-text-first">
      <div class="row4-left" >
        <h3><?=Yii::t('frontend','We check availability');?></h3>
        <p><?=Yii::t('frontend','{site-title} will invite participants to share their availability and preferences for times and places so you don\'t have to.',['site-title'=>Yii::$app->params['site']['title']]);?></p>
      </div>
    </div>
  </div>
  <!-- row 5 choosing date time and place -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row5-left " >
        <?= Html::img(Url::to('/img/home/choose.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6">
      <div class="row5-right pull-left" >
        <h3><?=Yii::t('frontend','We publish the schedule');?></h3>
        <p><?=Yii::t('frontend','When you\'re ready, finalize the time and location and we\'ll email details to everyone. You can also allow participants or designate additional organizers to make the final decisions.');?></p>
      </div>
    </div>
  </div>
  <!-- row 6 export to calendar -->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-md-push-6 col-lg-6 col-lg-push-6 ">
      <div class="row6-right pull-left" >
        <?= Html::img(Url::to('/img/home/exportcal.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-md-pull-6 col-lg-6 col-lg-pull-6  hiw-text-first">
      <div class="row6-left" >
        <h3><?=Yii::t('frontend','Automatically add to your calendar');?></h3>
        <p><?=Yii::t('frontend','Once you\'ve finalized the schedule, it\'s easy to add the event to your calendar, complete with links and a map.');?></p>
      </div>
    </div>
  </div>
  <!-- row 7 - reminders-->
  <div class="row ">
    <div class="col-xs-12 col-md-6 col-lg-6 ">
      <div class="row7-left " >
        <?= Html::img(Url::to('/img/home/reminder.gif'), ['class'=>'img-responsive centered']) ?>
      </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-6 hiw-text-second">
      <div class="row7-right pull-left" >
        <h3><?=Yii::t('frontend','Reminders');?></h3>
        <p><?=Yii::t('frontend','As the event nears, participants receive regular reminders via email or text message. Reminders are fully customizable.');?></p>
      </div>
    </div>
  </div>
  <div class="centered">
    <p><?= Html::a(Yii::t('frontend','Plan your date'),['site/signup'], ['class' => 'btn btn-lg btn-success','title'=>Yii::t('frontend','schedule your first meeting')]); ?></p>
  </div>
</div>
