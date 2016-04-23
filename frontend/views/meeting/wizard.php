<?php
use yii\helpers\Html;
use yii\helpers\Url;
$wizard_config = [
    'id' => 'stepwizard',
    'steps' => [
        1 => [
            'title' => 'Subject',
            'icon' => 'glyphicon glyphicon-envelope',
            'content' => '<h3>'.Yii::t('frontend','Subject').'</h3><p>'.Yii::t('frontend','What\'s the purpose of the meeting?').'</p>',
            'skippable' => false,

        ],
        2 => [
            'title' => 'Who',
            'icon' => 'glyphicon glyphicon-user',
            'content' => '<h3>'.Yii::t('frontend','Participants').'</h3><p></p>',
            //'skippable' => true,
        ],
        3 => [
            'title' => 'When',
            'icon' => 'glyphicon glyphicon-calendar',
            'content' => '<h3>'.Yii::t('frontend','When').'</h3><p>'.Yii::t('frontend','aaa').'</p>',
        ],
        4 => [
            'title' => 'Where',
            'icon' => 'glyphicon glyphicon-globe',
            'content' => '<h3>'.Yii::t('frontend','Where').'</h3><p>'.Yii::t('frontend','aaa').'</p>',
            'buttons' => [
                 'save' => [
                     'title' => 'Finish',
                     'options' => [
                         //'class' => 'disabled'
                     ],
                  ],
             ],
        ],
    ],
    'complete_content' => Yii::t('frontend','Your invitation has been sent.'), // Optional final screen
    'start_step' => 1, // Optional, start with a specific step
];
?>
<?= \drsdre\wizardwidget\WizardWidget::widget($wizard_config); ?>
