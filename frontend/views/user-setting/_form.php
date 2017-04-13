<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\components\MiscHelpers;
/* @var $this yii\web\View */
/* @var $model frontend\models\UserSetting */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="tz_success" id="tz_success">
<div id="w4-tz-success" class="alert-success alert fade in">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::t('frontend','Your timezone has been saved successfully.') ?>
</div>
</div>
<div class="tz_warning" id="tz_alert">
<div id="w4-tz-info" class="alert-info alert fade in">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<?= Yii::t('frontend','Would you like us to change your timezone setting to <span id="tz_new"></span>?') ?>
</div>
</div>

<div class="user-setting-form">
    <?php
    $form = ActiveForm::begin();
         ?>         
         <?= Html::activeHiddenInput($model, 'tz_dynamic',['id'=>'tz_dynamic']); ?>
        <div class="col-md-8">
         <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist">
           <li id="tab_general" class="<?= ($model->tab=='general'?'active':'') ?>"><a href="#general" role="tab" data-toggle="tab"><?= Yii::t('frontend','General Settings') ?></a></li>
           <li id="tab_preferences" class="<?= ($model->tab=='preferences'?'active':'') ?>"><a href="#preferences" role="tab" data-toggle="tab"><?= Yii::t('frontend','Meeting Preferences') ?></a></li>
           <li id="tab_guide" class="<?= ($model->tab=='guide'?'active':'') ?>"><a href="#guide" role="tab" data-toggle="tab"><?= Yii::t('frontend','Meeting Guide') ?></a></li>
         </ul>
         <!-- Tab panes -->
         <div class="tab-content">
           <div class="tab-pane <?= ($model->tab=='general'?'active':'') ?> vertical-pad" id="general">

               <?php /*= $form->field($model, 'reminder_eve')->checkBox(['label' => Yii::t('frontend','Send final reminder the day before a meeting'), 'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); */?>

               <?php /*= $form->field($model, 'reminder_hours')
                       ->dropDownList(
                           $model->getEarlyReminderOptions(),
           	                ['prompt'=>Yii::t('frontend','When would you like your early reminder?')]
           	            )->label(Yii::t('frontend','Early reminders')) */?>
                        <?php
                        echo $form->field($model, 'timezone')
                                ->dropDownList(
                                    $timezoneList,           // Flat array ('id'=>'label')
                                    ['options' => [$model->timezone => ['Selected'=>'selected']],
                                      'prompt'=>'Please select your local timezone below',
                                    'id'=>'tz_combo']
                                );
                                ?>
                      <span class="setting-label">
                      <?= $form->field($model, 'schedule_with_me')->checkbox(['label' =>Yii::t('frontend','Display your ').Html::a(Yii::t('frontend','schedule with me page'),['/'.$user->username]),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
                      <?= $form->field($model, 'contact_share')->checkbox(['label' =>Yii::t('frontend','Share my contact information with meeting participants'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>

                      <?= $form->field($model, 'no_updates')->checkbox(['label' =>Yii::t('frontend','Turn off emails about site upgrades'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
                      <?= $form->field($model, 'no_newsletter')->checkbox(['label' =>Yii::t('frontend','Turn off occasional newsletters about our service'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
                      <?= $form->field($model, 'no_email')->checkbox(['label' =>Yii::t('frontend','Turn off all email'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
</span>
                </div>
           <div class="tab-pane <?= ($model->tab=='preferences'?'active':'') ?> vertical-pad" id="preferences">

             <strong><?= Yii::t('frontend','Allow participants to:'); ?></strong><br /><br />
             <?= $form->field($model, 'participant_add_place')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_add_date_time')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_add_activity')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_choose_place')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_choose_date_time')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_choose_activity')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_finalize')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_request_change')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_reopen')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
            </div> <!-- end of upload meeting-settings tab -->
            <div class="tab-pane <?= ($model->tab=='guide'?'active':'') ?> vertical-pad" id="guide">
              <span class="setting-label">
              <?= $form->field($model, 'guide')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
            </span>
            </div> <!-- end of upload meeting-guide tab -->
           <div class="form-group">
               <?= Html::submitButton(Yii::t('frontend', 'Save Settings'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
           </div>
         </div> <!-- end tab content -->
         </div> <!--end left col -->
      <?php ActiveForm::end();
      $this->registerJsFile(MiscHelpers::buildUrl().'/js/jstz.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
      $this->registerJsFile(MiscHelpers::buildUrl().'/js/user_setting.js',['depends' => [\yii\web\JqueryAsset::className()]]);
      ?>

</div>
