<?php
  use yii\helpers\Html;
  use yii\helpers\Url;
  use frontend\assets\CalendarAsset;
  CalendarAsset::register($this);
?>

<div class="calendarChooser">
<table >
  <?php
    $numberOfDays=30;
    $start_day = time();
    $current_time = $start_day;
      ?>
      <thead>
        <tr>
          <th></th> <!-- time column -->
        <?php
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday'];
        for ($index_day=1; $index_day<$numberOfDays;$index_day++) {
          // create the header row
        ?>
        <th><div class="nameMonth"><?= date('M',$current_time);?></div><div class="dayOfMonth"><?= date('j',$current_time);?></div><?= $days[date('w',$current_time)];?></th>
        <?
        $current_time+=24*3600; // add one day
      }
     ?>
        </tr>
      </thead>
    <tbody>

    <?php
      $start_hour = 0;
      $current_hour = date('G');
      $midnight = mktime(0, 0, 0);
      for($hour_index=0;$hour_index<24;$hour_index++) {
        ?>
        <tr>
          <td class="hourOfDay"><?= date('g a',$midnight+($hour_index*3600))?></td>
          <?php
          for ($index_day=1; $index_day<$numberOfDays;$index_day++) {
            ?>
            <td><div class="dayCell apple2"></div></td>
            <?
          }
          ?>
      <?php
        }
      ?>
        </tr>
    </tbody>
  </table>
</div>
