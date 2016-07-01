<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?php echo Yii::t('frontend','Cancel this meeting?');?></h1>
  <p>
  <?php
  echo Yii::t('frontend','Are you sure you want to cancel the meeting  entitled {title}?',['title'=>$model->subject]);
 ?>
</p>
<div class="form-group">
  <span class="button-pad">
    <?= Html::a(Yii::t('frontend','Yes, cancel'), ['/meeting/cancel', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
  </span><span class="button-pad">
    <?= Html::a(Yii::t('frontend','No, keep'), ['/meeting/view', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
  </span>
</div>
