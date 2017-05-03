<?php
use yii\helpers\Html;
use frontend\models\Friend;
use frontend\models\Address;
use yii\widgets\ActiveForm;
use \kartik\typeahead\Typeahead;
//use frontend\assets\ComboAsset;
//ComboAsset::register($this);
/* @var $this yii\web\View */
/* @var $model frontend\models\Participant */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="participant-form">
    <?php $form = ActiveForm::begin([
      'id'=> 'participant-form',
      //'enableAjaxValidation' => 'true',
    ]); ?>
    <?= $form->errorSummary($participant); ?>
    <div class="row" id="whoEmail">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <?php
          //echo $form->field($participant, 'new_email',['enableAjaxValidation' => true,'options'=>['class'=>'fieldLeftFull']])->textInput(['placeholder' => "enter an email address to invite someone new",'id'=>'new_email'])->label(Yii::t('frontend','Invite someone new'));
          echo $form->field($participant, 'new_email',['enableAjaxValidation' => true,'options'=>['class'=>'fieldLeftFull']])->textarea(['rows'=>6,'placeholder' => Yii::t('frontend','enter one or more email addresses (separated by commas or on different lines)'),'id'=>'new_email'])->label(Yii::t('frontend','Invite people'));
        ?>
        <div class="form-group">
          <span class="button-pad">
            <?= Html::a(Yii::t('frontend','Add Participant(s)'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addParticipant('.$participant->meeting_id.',"email");'])  ?>
          </span><span class="button-pad">
            <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'closeParticipant();'])  ?>
          </span>
        </div>
      </div>
    </div>
    <div class="row hidden" id="whoFavorites">
      <div class="col-xs-12 col-md-12 col-lg-12">
    <?php
    // to do - replace with Friend::getFriendList
    // to do - pre-load this array in meetingcontroller, replace $friendCount
    $friendsEmail=[];
    $friendsId=[];
    $fq = Friend::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    // to do - add a display name field for right side of input
    $fa = Address::find()
      ->select(['id','email'])
      ->where(['user_id'=>Yii::$app->user->getId()])
      ->limit(100)
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
      // below: chg meetingjs if change string
      ?>
      <p><strong><?= Yii::t('frontend','Choose from your friends');?></strong></p>
      <select class="combobox input-large form-control" id="participant-email" name="Participant[email]" >
      <option value="" selected="selected"><?= Yii::t('frontend','type or click to choose friends'); ?></option>
      <?php
      foreach ($friendsEmail as $email) {
      ?>
        <option value="<?= $email ?>"><?= $email ?></option>
      <?php
        }
      ?>
      </select>
      <?php
    } else {
      ?>
      <p><strong><?= Yii::t('frontend','Once you invite people to meetups, you\'ll be able to add them here');?></strong></p>
      <?php
    }
    ?>

  <p></p>
  <?php
  if (count($friendsEmail)>0) {
  ?>
    <div class="form-group">
      <span class="button-pad">
        <?= Html::a(Yii::t('frontend','Add Participant'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addParticipant('.$participant->meeting_id.',"fav");'])  ?>
      </span><span class="button-pad">
        <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'closeParticipant();'])  ?>
      </span>
    </div>
<?php
  }
?>
  </div>
</div> <!-- end row -->
<?php ActiveForm::end(); ?>
<hr />
</div> <!-- end div -->
<?= $this->registerJs("$(document).ready(function(){ $('.combobox').combobox() });"); ?>
<?= Html::hiddenInput('textChooseFriends',Yii::t('frontend','type or click arrow to choose friends'),['id'=>'textChooseFriends']); ?>
