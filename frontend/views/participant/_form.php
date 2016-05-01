<?php

use yii\helpers\Html;
use frontend\models\Friend;
use yii\widgets\ActiveForm;
use \kartik\typeahead\Typeahead;

/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="participant-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <p>Email address:</p>
    <?php
    $friends=['jeff@lookahead.me'];
    /*$friendId=[];
    $fq = Friend::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    foreach ($fq as $f) {
      $friends[]=$f->user->email; // get constructed name fields
      $friendId[]=$f->id;
    }
    $friends[]='Jeff Reifman <jeff@lookahead.me>';
    $friendId[]=1;

    echo $form->field($model, 'email')->widget(Typeahead::classname(), [
        'options' => ['placeholder' => '-- type in an email address --'],
        'scrollable'=>true,
        'pluginOptions' => ['highlight'=>true],
        'dataset' => [
            [
                'local' => $friends,
                'limit' => 10
            ]
        ]
    ]);
    */
      // preload friends into array
      echo yii\jui\AutoComplete::widget([
          'model' => $model,
          'attribute' => 'email',
          'clientOptions' => [
          'source' => $friends,
           ],
          ]);

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
