<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    // available languages
    // 'ar','de','es','it','iw','ja','yi','zh-CN'
    'language' => 'en', // english
    // 'homeUrl' => '/mp',
    //'catchAll' => ['site/offline'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'rollbar' => [
          'class' => 'baibaratsky\yii\rollbar\Rollbar',
          'accessToken' => '23d92e0778614e53afb4eaf981a80ae9',
          // 'ignoreExceptions' => [
      //         ['yii\web\HttpException', 'statusCode' => [400, 404]],
      //         ['yii\web\HttpException', 'statusCode' => [403], 'message' => ['This action is forbidden']],
      // ],
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
