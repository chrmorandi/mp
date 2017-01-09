<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log','\common\components\SiteHelper'],
    'modules' => [],
    'components' => [
      'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => __DIR__ . '/../runtime/sessions',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
              'name' => '_backendUser',
              'path'=>'/backend/web',
              'domain'=>'office.meetingplanner.io',
              'expire'=>time() + 86400 * 7,
              'secure'=>true,
            ]
        ],
      'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'log' => [
          'traceLevel' => YII_DEBUG ? 3 : 0,
          'targets' => [
              [
                  'class' => 'notamedia\sentry\SentryTarget',
                  'dsn' => 'http://'.$config['sentry_key_public'].':'.$config['sentry_key_private'].'@sentry.io/'.$config['sentry_id'],
                  'levels' => ['error', 'warning'],
                  'context' => true, // Write the context information. The default is true.
              ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
    'defaultRoute' => '/site',
];
