<?php
/* @var $this yii\web\View */

$this->title = Yii::t('backend','Meeting Planner');
?>
<div class="site-index">

        <h1>Real Time Data</h1>

    <div class="body-content">
      <p>Current Statistics</p>
      <h3>Meetings</h3>
      <?php
      foreach ($data->meetings as $m) {
          echo $m->status.' -> '.$m->dataCount.'<br />';
      }

      ?>
<h3>People</h3>
<?php
foreach ($data->users as $m) {
    echo $m->status.' -> '.$m->dataCount.'<br />';
}
?>
<h3>Places</h3>
<?php
foreach ($data->userPlaces as $up) {
    echo $up->user_id.' -> '.$up->dataCount.'<br />';
}

echo $data->avgUserPlaces.'<br />';

?>

</div>
