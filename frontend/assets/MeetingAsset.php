<?php
namespace frontend\assets;
use yii\web\AssetBundle;

class MeetingAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
      'css/jquery-ui.min.css',
      'css/bootstrap-combobox.css',
      'css/shepherd-theme-arrows.css',
      'css/calendar.css',
    ];
    public $js = [
      'js/jquery-ui.min.js',
      'js/touch-punch-min.js',
      'js/meeting.js',
      'js/meeting_time.js',
      'js/jstz.min.js',
      'js/calendar.js',
      'js/bootstrap-combobox.js',
      'js/create_place.js',
      'js/tether.min.js',
      'js/shepherd.min.js',
      'js/meeting_tour.js',
      'js/flags.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $publishOptions = [];
}
