<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\jui\DatePickerLanguageAsset;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImpeachmentAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/impeachment.css',
    ];
    //public $autoGenerate =true;
    public $js = [
      'js/jstz.min.js',
      'js/impeachment.js',
    ];

      public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

/*    public function init()
   {
       //$this->js[] = 'jquery.ui.datepicker-' . Yii::$app->language . '.js';
       parent::init();
   }*/
}
