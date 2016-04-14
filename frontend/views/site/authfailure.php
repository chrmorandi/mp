<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserSetting */

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Feature Unavailable')];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-unavailable">

    <h1>Sorry, Authorization Failed</h1>
    <div class="col-md-8">

    <p>
      Unfortunately, the authorization in the link you clicked failed. Please <?= Html::a(Yii::t('frontend', 'contact us'),['site/contact']) ?> us if it's super important to you.
    </p>

    </div> <!-- end col-md-8 -->

</div>
