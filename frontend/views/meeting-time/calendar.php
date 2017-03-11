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
  $midnight = mktime(0, 0, 0);
  $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];
?>
<div class="calendarContainer">

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
            <div class="nameMonth"><?= date('M',$current_time);?></div>
            <div class="dayOfMonth"><?= date('j',$current_time);?></div>
            <?= $days[date('w',$current_time)];?>
          </th>
        <?php
          $current_time+=24*3600; // add one day
        }
        ?>
      </tr>
    </thead>
    <tbody>
    <?php
      for($hour_index=0;$hour_index<24;$hour_index++) {
        ?>
        <tr>
          <td class="hourOfDay"><?= date('g a',$midnight+($hour_index*3600))?></td>
          <?php
          for ($index_day=1; $index_day<$numberOfDays;$index_day++) {
            ?>
            <td><div class="dayCell apple2"></div></td>
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

</div> <!-- calendarContainer -->
