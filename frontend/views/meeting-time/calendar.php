<?php
  use yii\helpers\Html;
  use yii\helpers\Url;
  use frontend\assets\CalendarAsset;
  CalendarAsset::register($this);
  $start_day = time();
  $current_time = $start_day;
  $start_hour = 0;
  $numberOfDays=30;
  $current_hour = date('G');
  $midnight = strtotime('today midnight')-24*3600; // mktime(0, 0, 0);
  $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];
?>
<div class="calendarContainer">
  <button id="create-user">Select Times</button>
    <div id="dialog-form" title="Select Dates and Times">
        <div class="calendarChooser">
        <table >
            <thead>
              <tr>
                <th></th> <!-- time column -->
                <?php
                  // create the header row
                  for ($index_day=1; $index_day<$numberOfDays; $index_day++) {
                ?>
                  <th>
                    <div class="nameMonth"><?= date('M',$midnight);?></div>
                    <div class="dayOfMonth"><?= date('j',$midnight);?></div>
                    <?= $days[date('w',$midnight)];?>
                  </th>
                <?php
                  $midnight+=24*3600; // add one day
                }
                ?>
              </tr>
            </thead>
            <tbody>
            <?php
              for($hour_index=7;$hour_index<24;$hour_index++) {
                $temp_7am = $midnight+(($hour_index-1)*3600);
                ?>
                <tr>
                  <td class="hourOfDay"><?= date('g a',$temp_7am)?></td>
                  <?php
                  for ($index_day=1; $index_day<$numberOfDays;$index_day++) {
                    $temp=$temp_7am+(24*3600*$index_day-1);
                    ?>
                    <td>
                      <div id="dc_<?= $temp ?>" class="dayCell"></div>
                      <div id="dc_<?= $temp+900 ?>" class="dayCell"></div>
                      <div id="dc_<?= $temp+1800 ?>" class="dayCell"></div>
                      <div id="dc_<?= $temp+2700 ?>" class="dayCell"></div>
                    </td>
                    <?php
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
