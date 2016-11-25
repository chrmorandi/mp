<?php
use frontend\web\Meeting;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingSetting */

$header = $model->meeting->getMeetingHeader();
$this->title = Yii::t('frontend', 'Update settings for '.$header);
$this->params['breadcrumbs'][] = ['label' => $header,'url'=>Url::home(true).'meeting/'.$model->meeting_id];
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Meeting Settings')];

?>
<div class="meeting-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <p><?php
      echo Yii::t('frontend','You can change the permission settings for this meeting below. Or, ');
      echo Html::a(Yii::t('frontend', 'change your default meeting preferences'),
        ['/user-setting/update', 'id' => Yii::$app->user->getId()]);
     echo Yii::t('frontend',' for all future meetings.');
     ?>
   </p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
