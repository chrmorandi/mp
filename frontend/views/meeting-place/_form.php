<?php
use yii\helpers\ArrayHelper;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\UserPlace;
use \kartik\typeahead\Typeahead;
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
      <div class="col-xs-12 col-lg-6">
        <h3>From your places</h3>
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
    <div class="col-xs-12 col-lg-6">
    <h4><center>- or -</center></h4>
  </div>
</div>
<div class="row">
<?php
  }
    ?>
    <div class="col-xs-12 col-lg-6">
    <h3>Use Google Maps</h3>
        <?= $form->field($model, 'searchbox')->textInput(['maxlength' => 255])->label('Type in an address, place or business known to Google Maps') ?>
        <?= BaseHtml::activeHiddenInput($model, 'name'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'google_place_id'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'location'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'website'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'vicinity'); ?>
        <?= BaseHtml::activeHiddenInput($model, 'full_address'); ?>
      </div>
  </div> <!-- end row -->
    <div class="row vertical-pad">
      <div class="col-xs-12 col-lg-6">
        <div class="form-group">
          <span class="button-pad">
            <?= Html::a(Yii::t('frontend','Add Meeting Place'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addPlace('.$model->meeting_id.');'])  ?>
          </span><span class="button-pad">
            <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelPlace();'])  ?>
         </span>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12 col-lg-6">
        <div id="map-canvas">
          <article></article>
        </div>
      </div>
    </div>
    <?php ActiveForm::end(); ?>
</div> <!-- end form -->
<?php
  $gpJsLink= 'https://maps.googleapis.com/maps/api/js?' . http_build_query(array(
                          'libraries' => 'places',
                          'key' => Yii::$app->params['google_maps_key'],
                  ));
  $this->registerJsFile($gpJsLink);

  $options = '{"componentRestrictions":{}}';
  // turned off "country":"us"
  // turned off "types":["establishment"]
  echo $this->registerJs("(function(){
        var input = document.getElementById('meetingplace-searchbox');
        var options = $options;
        searchbox = new google.maps.places.Autocomplete(input, options);
        setupListeners('meetingplace');
})();" , \yii\web\View::POS_END );
// 'setupBounds('.$bound_bl.','.$bound_tr.');
?>
