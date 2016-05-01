<?php
use yii\helpers\ArrayHelper;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\assets\ComboAsset;
ComboAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\MeetingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('frontend', 'Meetings');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $this->title ?></h1>

<div class="meeting-place-form">

    <?php $form = ActiveForm::begin();
    $this->registerJs("$(document).ready(function(){
  $('.combobox').combobox()
});");
?>

    <div class="form-group">
        <label>Into this</label>
        <select class="combobox input-large form-control" name="normal">
          <option value="" selected="selected">Select a State</option>
          <option value="AL">Alabama</option>
          <option value="AK">Alaska</option>
          <option value="AZ">Arizona</option>
          <option value="AR">Arkansas</option>
        </select>
      </div>


    <?php ActiveForm::end(); ?>
  </div>
