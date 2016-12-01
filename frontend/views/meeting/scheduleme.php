<?php
use yii\helpers\Html;
?>

<div class="scheduleme-top">
<div class="row">
  <div class="col-lg-8 col-lg-offset-2 col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2">
    <div class="centered schedule-me">
    <?php
     if ($userprofile->avatar<>'') {
       echo '<img src="'.Yii::getAlias('@web').'/uploads/avatar/sqr_'.$userprofile->avatar.'" class="profile-image"/>';
     } else {
       echo \cebe\gravatar\Gravatar::widget([
            'email' => $user->email,
            'options' => [
                'class'=>'profile-image',
                'alt' => $user->username,
            ],
            'size' => 128,
        ]);
     }
     ?>
     <h1><?= $displayName ?></h1>
     <p class="lead">
       <?php if (Yii::$app->user->isGuest) { ?>
       <?= Html::a(Yii::t('frontend','Schedule a meeting with me'),['site/signup'])?>
       <?php } else { ?>
         <?= Html::a(Yii::t('frontend','Schedule a meeting with me'),['meeting/create','with'=>$user->username])?>
        <?php } ?>
       <p>
   </div>
  </div>
</div>
</div>
