<?php
use yii\helpers\Html;
use common\components\MiscHelpers;
  $start_day = time();
  $current_time = $start_day;
  $start_hour = 0;
  $numberOfDays=45;
  $current_hour = date('G');
  $months= [
    'zero-unused',
    Yii::t('frontend','Jan'),
    Yii::t('frontend','Feb'),
    Yii::t('frontend','Mar'),
    Yii::t('frontend','Apr'),
    Yii::t('frontend','May'),
    Yii::t('frontend','Jun'),
    Yii::t('frontend','Jul'),
    Yii::t('frontend','Aug'),
    Yii::t('frontend','Sep'),
    Yii::t('frontend','Oct'),
    Yii::t('frontend','Nov'),
    Yii::t('frontend','Dec'),
  ];
  $days = [Yii::t('frontend','Sunday'), Yii::t('frontend','Monday'), Yii::t('frontend','Tuesday'), Yii::t('frontend','Wednesday'),Yii::t('frontend','Thursday'),Yii::t('frontend','Friday'),Yii::t('frontend','Saturday')];
  // server time zone
  //$dateTimeZoneServer = new \DateTimeZone('America/Los_Angeles');
  $dateTimeZoneUser =new \DateTimeZone($timezone);
  $todayStart = new \DateTime('today midnight', $dateTimeZoneUser);
  //$todayStartServer = new \DateTime('today midnight', $dateTimeZoneServer);
  //$zoneOffset = ($todayStart->getTimestamp()-$todayStartServer->getTimestamp());
  $midnight = $todayStart->getTimestamp();
  //echo Html::hiddenInput('zoneOffset',$zoneOffset,['id'=>'zoneOffset']); // hidden variable
?>
<div class="tz_success" id="tz_success">
  <div id="w4-tz-success" class="alert-success alert fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <?= Yii::t('frontend','Your timezone has been updated successfully.') ?>
  </div>
</div>
<div class="tz_warning" id="tz_alert">
  <div id="w4-tz-info" class="alert-info alert fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <?= Yii::t('frontend','Would you like us to change your timezone setting to <span id="tz_new"></span>?') ?>
  </div>
</div>
<div class="meeting-time-form">
<div class="calendarContainer">
    <div id="dialog-form" title="<?= Yii::t('frontend','Select One or More Dates and Times'); ?>">
        <div class="calendarChooser">
        <table >
            <thead>
              <tr>
                <th></th> <!-- time column -->
                <?php
                  // create the header row
                  $dayStamp = $midnight;
                  //setlocale(LC_ALL, Yii::$app->language);
                  for ($index_day=1; $index_day<$numberOfDays; $index_day++) {
                ?>
                  <th>
                    <div class="nameMonth">&nbsp;&nbsp;<?= $months[date('n',$dayStamp)];?></div>
                    <div class="dayOfMonth"><?= date('j',$dayStamp);?></div>
                    <div class="dayOfWeek">&nbsp;&nbsp;<?= $days[date('w',$dayStamp)];?>&nbsp;&nbsp;</div>
                  </th>
                <?php
                  $dayStamp+=24*3600;
                }
                ?>
              </tr>
            </thead>
            <tbody>
            <?php
              for($hour_index=7;$hour_index<24;$hour_index++) {
                $hourStamp = $midnight+($hour_index*3600);
                $adjHourStamp=$hourStamp-9*3600;
                ?>
                <tr>
                  <td class="hourOfDay">
                    <?php
                      if ($hour_index>12) {
                        echo ($hour_index-12).' pm';
                      } else if ($hour_index==12) {
                        echo '12 pm';
                      } else {
                        echo ($hour_index).' am';
                      }
                     ?>
                  </td>
                  <?php
                  $cellStamp=$hourStamp;
                  for ($index_day=1; $index_day<$numberOfDays;$index_day++) {
                    ?>
                    <td>
                      <div id="c_<?= $cellStamp.'_'.$hour_index ?>" class="dayCell"></div> <!--  -->
                      <div id="c_<?= ($cellStamp+900).'_'.$hour_index ?>" class="dayCell"></div>
                      <div id="c_<?= ($cellStamp+1800).'_'.$hour_index ?>" class="dayCell"></div>
                      <div id="c_<?= ($cellStamp+2700).'_'.$hour_index ?>" class="dayCell"></div>
                    </td>
                    <?php
                    $cellStamp+=24*3600;
                  }
                  ?>
              <?php
                }
              ?>
                </tr>
            </tbody>
          </table>
        </div> <!-- calendarChooser -->
            <!-- Allow form submission with keyboard without duplicating the dialog button -->
            <input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
        </div>
</div> <!-- calendarContainer -->
</div> <!-- end container -->
<?= Html::hiddenInput('buttonSave',Yii::t('frontend','Save times'),['id'=>'buttonSave']); ?>
<?= Html::hiddenInput('buttonCancel',Yii::t('frontend','Cancel'),['id'=>'buttonCancel']); ?>
