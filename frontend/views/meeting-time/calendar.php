<?php
  use yii\helpers\Html;
  use yii\helpers\Url;
  use frontend\assets\CalendarAsset;
  CalendarAsset::register($this);
  $numberOfDays=30;
  $start_day = time();
  $current_time = $start_day;
  $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];
?>
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
  </table>
</div>
