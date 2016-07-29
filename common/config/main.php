<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    // available languages
    // 'ar','de','es','it','iw','ja','yi','zh-CN'
    'language' => 'en', // english
    // 'homeUrl' => '/mp',
    //'catchAll' => ['site/offline'],
    'components' => [
        'urlManager' => [
                  'class' => 'yii\web\UrlManager',
                  'enablePrettyUrl' => true,
                  'showScriptName' => 'false',
                  //'enableStrictParsing' => false,
                  'rules' => [
                    'place' => 'place/index',
                    'place/yours' => 'place/yours',
                    'place/create' => 'place/create',
                    'place/create_geo' => 'place/create_geo',
                    'place/create_place_google' => 'place/create_place_google',
                    'place/view/<id:\d+>' => 'place/view',
                    'place/update/<id:\d+>' => 'place/update',
                    'place/<slug>' => 'place/slug',
                    '<controller:\w+>/<id:\d+>' => '<controller>/view',
                      '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                      '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                      'defaultRoute' => '/site/index',
                  ],
              ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                'frontend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                'backend*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
    ],
];
