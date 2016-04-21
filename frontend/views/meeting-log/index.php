<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use frontend\models\Meeting;
use common\models\User;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meeting Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php Pjax::begin(); ?>
<div class="meeting-log-index">
    <h1><?php echo  Html::encode($this->title) ?></h1>
<?php Pjax::end(); ?></div>
