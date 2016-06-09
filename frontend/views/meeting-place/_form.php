<?php
use yii\helpers\ArrayHelper;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\UserPlace;
use \kartik\typeahead\Typeahead;
use frontend\assets\MapAsset;
MapAsset::register($this);
use frontend\assets\ComboAsset;
ComboAsset::register($this);
//use frontend\assets\ComboboxAsset;
//Combobox::register($this);

/* @var $this yii\web\View */
/* @var $model frontend\models\MeetingPlace */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meeting-place-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

<?php
  $ups=[];
  $up = UserPlace::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
  if (count($up)>0) {
    ?>

    <div class="row">
      <div class="col-md-6">
        <h3>Choose one of your places</h3>
<select class="combobox input-large form-control" id="meetingplace-place_id" name="MeetingPlace[place_id]">
  <option value="" selected="selected"><?= Yii::t('frontend','type or click at right to see places')?></option>
    <?php
    foreach ($up as $p) {
      $ups[]=$p->place->name;
      ?>
      <option value="<?= $p->id;?>"><?= $p->place->name;?></option>
      <?php
    }
    ?>
</select>
  </div>
</div> <!-- end row -->
<div class="row">
    <div class="col-md-6">
    <h3>- or -</h3>
  </div>
</div>
<div class="row">
<?php
  }
    ?>
    <div class="col-md-6">
    <h3>Choose from Google Places</h3>
      <p>Type in a place or business known to Google Places:</p>
        <?= $form->field($model, 'searchbox')->textInput(['maxlength' => 255])->label('Place') ?>
      </div>
      <div class="col-md-4">
        <div id="map-canvas">
          <article></article>
        </div>
      </div>
        <?= BaseHtml::activeHiddenInput($model, 'name'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'google_place_id'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'location'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'website'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'vicinity'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'full_address'); ?>
  </div> <!-- end row -->
    <div class="row vertical-pad">
      <div class="form-group">
          <?= Html::submitButton($model->isNewRecord ? Yii::t('frontend', 'Submit') : Yii::t('frontend', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
          <?= Html::a(Yii::t('frontend','Cancel'), ['/meeting/view', 'id' => $model->meeting_id], ['class' => 'btn btn-danger']) ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>

</div> <!-- end form -->
