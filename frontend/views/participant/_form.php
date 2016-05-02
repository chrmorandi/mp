<?php

use yii\helpers\Html;
use frontend\models\Friend;
use yii\widgets\ActiveForm;
use \kartik\typeahead\Typeahead;
use frontend\assets\ComboAsset;
ComboAsset::register($this);

/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="participant-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <div class="row">
      <div class="col-md-6">

    <?php
    //$friends=['1'=>'jeff@lookahead.me'];
    //$up = UserPlace::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    //foreach ($up as $p) {
      //$ups[]=$p->place->name;
      //foreach ($friends as $id=>$email) {
      ?>
      <!-- <option value="< ?= $id;?>">< ?= $email;?></option>-->
      <?php
      //}
    ?>
    <!--</select>-->

    <?php
    $friendsEmail=[];
    $friendsId=[];
    $fq = Friend::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    foreach ($fq as $f) {
      $friendsEmail[]=$f->friend->email; // get constructed name fields
      $friendsId[]=$f->id;
    }
    //var_dump($friendsEmail);exit;
    //$friendsEmail[]='Jeff Reifman <jeff@lookahead.me>';
    //$friendId[]=1;
    echo $form->field($model, 'email')->widget(Typeahead::classname(), [
        'options' => ['placeholder' => '-- type in an email address --'],
        'scrollable'=>true,
        'pluginOptions' => ['highlight'=>true],
        'dataset' => [
            [
                'local' => $friendsEmail,
                'limit' => 10
            ]
        ]
    ]);
/*
      // preload friends into array
      echo yii\jui\AutoComplete::widget([
          'model' => $model,
          'attribute' => 'email',
          'clientOptions' => [
          'source' => $friends,
           ],
          ]);
*/
    ?>

    <p></p>
    <!-- todo - offer drop down of friends -->

    <?php // $form->field($model, 'meeting_id')->textInput() ?>

    <?php // $form->field($model, 'participant_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Invite') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
