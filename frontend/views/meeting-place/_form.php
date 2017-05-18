<?php
use yii\helpers\ArrayHelper;
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
  // to do - optimize with meeting controller
  $up = UserPlace::find()->where(['user_id'=>Yii::$app->user->getId()])->all();
    ?>
    <div class="row" id="wherePlaces">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <?= $form->field($model, 'searchbox')->textInput(['maxlength' => 255])->label(Yii::t('frontend','Type an address, place or business')) ?>
        <?= Html::activeHiddenInput($model, 'name'); ?>
        <?= Html::activeHiddenInput($model, 'google_place_id'); ?>
        <?= Html::activeHiddenInput($model, 'location'); ?>
        <?= Html::activeHiddenInput($model, 'website'); ?>
        <?= Html::activeHiddenInput($model, 'vicinity'); ?>
        <?= Html::activeHiddenInput($model, 'full_address'); ?>
      </div>
    </div>
    <div class="row" id="whereFavorites" class="hidden">
      <div class="col-xs-12 col-md-12 col-lg-12">
        <?php
        if (count($up)==0) {
          ?>
          <p><em><?= Yii::t('frontend','As you add places for meetings, they will be added to your favorites below:');?></em></p>
          <?php
        }
          ?>
          <p><strong><?= Yii::t('frontend','Choose from previously used places');?></strong></p>
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
    <div class="row vertical-pad">
      <div class="col-xs-12 col-lg-6">
        <div class="form-group">
          <span class="button-pad">
            <?= Html::a(Yii::t('frontend','Add a place'), 'javascript:void(0);', ['class' => 'btn btn-success','onclick'=>'addPlace('.$model->meeting_id.');'])  ?>
          </span><span class="button-pad">
            <?= Html::a(Yii::t('frontend','Cancel'), 'javascript:void(0);', ['class' => 'btn btn-danger','onclick'=>'cancelPlace();'])  ?>
         </span>
        </div>
      </div>
    </div>
    <div class="row hidden" id="mapRow">
      <div class="col-xs-12 col-md-12 col-lg-6">
        <div id="map-canvas">
          <article></article>
        </div>
      </div>
    </div>
    <?php ActiveForm::end(); ?>
</div> <!-- end form -->
<?php
  $gpJsLink= 'https://maps.googleapis.com/maps/api/js?' . http_build_query([
                          'libraries' => 'places',
                          'key' => Yii::$app->params['google_maps_key'],
                          'language'=>Yii::$app->language,
                  ]);
  $this->registerJsFile($gpJsLink);
  $options = '{}';
  // off {"componentRestrictions":{}}
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
