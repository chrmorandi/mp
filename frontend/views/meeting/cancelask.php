<?php

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Yii::t('frontend','Cancel Meeting');?></h1>
  <p>
  <?php
  echo Yii::t('frontend','Are you sure you want to cancel this meeting ({title})',['title'=>$model->subject]);
 ?>
</p>
