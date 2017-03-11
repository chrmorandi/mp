<?php
namespace frontend\assets;
use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
      'css/bootstrap-combobox.css',
      'css/calendar.css',
      'css/jquery-ui.css',
    ];
    public $js = [
      'js/touch-punch.js',
      'js/jquery-ui.js',
      'js/calendar.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public $publishOptions = [];
}
