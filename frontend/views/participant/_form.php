<?php

use yii\helpers\Html;
use frontend\models\Friend;
use frontend\models\Address;
use yii\widgets\ActiveForm;
//use \kartik\typeahead\Typeahead;
use frontend\assets\ComboAsset;
ComboAsset::register($this);

/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="participant-form">
    <?php $form = ActiveForm::begin([
      'id'=> 'participant-form',
      //'enableAjaxValidation' => 'true',
    ]); ?>

    <?= $form->errorSummary($model); ?>

    <div class="row">
      <div class="col-md-6">
        <?php
        echo $form->field($model, 'new_email',['enableAjaxValidation' => true])->textInput(['placeholder' => "enter an email address to invite someone new"])->label(Yii::t('frontend','Invite Someone New'))
        ?>
    <?php
    // to do - replace with Friend::getFriendList
    $friendsEmail=[];
    $friendsId=[];
    $fq = Friend::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    // to do - add a display name field for right side of input
    $fa = Address::find()
      ->select(['id','email'])
      ->where(['user_id'=>Yii::$app->user->getId()])
      ->limit(5000)
      ->all();
    foreach ($fq as $f) {
      $friendsEmail[]=$f->friend->email; // get constructed name fields
      $friendsId[]=$f->id;
    }
    foreach ($fa as $f) {
      $friendsEmail[]=$f->email; // get constructed name fields
      $friendsId[]=$f->id;
    }
    if (count($friendsEmail)>0) {
      ?>
      <p><strong>Choose From Your Friends</strong></p>
      <select class="combobox input-large form-control" id="participant-email" name="Participant[email]">
      <option value="" selected="selected"><?= Yii::t('frontend','type or click to choose friends')?></option>
      <?php
      foreach ($friendsEmail as $email) {
      ?>
        <option value="<?= $email;?>"><?= $email;?></option>
      <?php
        }
      ?>
      <?php
    }
    ?>
  </select>
  <p></p>    
    <div class="form-group">
      <span class="button-pad">
        <?php echo Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
      </span><span class="button-pad">
        <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view', 'id' => $model->meeting_id], ['class' => 'btn btn-danger']) ?>
      </span>
    </div>
    <?php ActiveForm::end(); ?>

</div>
