<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'components' => [
      'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => __DIR__ . '/../runtime/sessions',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => ['user','user-token', 'v1/user-token']],                  
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
            'errorAction' => 'user/error',
        ],
    ],
    'params' => $params,
    'defaultRoute' => '/user',
];
