<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-setting-form">
    <?php
    $form = ActiveForm::begin();
         ?>
        <div class="col-md-8">
         <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist">
           <li class="active"><a href="#general" role="tab" data-toggle="tab"><?= Yii::t('frontend','General Settings') ?></a></li>
           <li><a href="#preferences" role="tab" data-toggle="tab"><?= Yii::t('frontend','Meeting Preferences') ?></a></li>
         </ul>
         <!-- Tab panes -->
         <div class="tab-content">
           <div class="tab-pane active vertical-pad" id="general">

               <?/*= $form->field($model, 'reminder_eve')->checkBox(['label' => Yii::t('frontend','Send final reminder the day before a meeting'), 'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); */?>

               <?/*= $form->field($model, 'reminder_hours')
                       ->dropDownList(
                           $model->getEarlyReminderOptions(),
           	                ['prompt'=>Yii::t('frontend','When would you like your early reminder?')]
           	            )->label(Yii::t('frontend','Early reminders')) */?>
                        <?php
                        echo $form->field($model, 'timezone')
                                ->dropDownList(
                                    $timezoneList,           // Flat array ('id'=>'label')
                                    ['options' => [$model->timezone => ['Selected'=>'selected']],
                                      'prompt'=>'Please select your local timezone below']
                                );
                                ?>
                  <?= $form->field($model, 'contact_share')->checkbox(['label' =>Yii::t('frontend','Share my contact information with meeting participants'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>

                  <?= $form->field($model, 'no_email')->checkbox(['label' =>Yii::t('frontend','Turn off all email'),'uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
                </div>
           <div class="tab-pane vertical-pad" id="preferences">
             <?= $form->field($model, 'participant_add_place')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_add_date_time')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_choose_place')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_choose_date_time')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>
             <?= $form->field($model, 'participant_finalize')->checkbox(['uncheck' =>  $model::SETTING_NO, 'checked' => $model::SETTING_YES]); ?>

            </div> <!-- end of upload meeting-settings tab -->
           <div class="form-group">
               <?= Html::submitButton(Yii::t('frontend', 'Save Settings'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
           </div>
         </div> <!-- end tab content -->
         </div> <!--end left col -->
      <?php ActiveForm::end(); ?>

</div>
