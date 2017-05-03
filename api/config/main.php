<?php
$config = parse_ini_file('/var/secure/mp.ini', true);
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/../../frontend/config/params.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    /*'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
    ],*/
    'components' => [
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
          'errorAction' => 'service/error',
      ],
      'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null && Yii::$app->request->get('suppress_response_code')) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'data' => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
        ],
      'request' => [
        'parsers' => [
          'application/json' => 'yii\web\JsonParser',
        ],
      ],
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
              'class' => 'yii\web\UrlManager',
              'enablePrettyUrl' => true,
              'showScriptName' => false,
              'rules' => [
                  '<controller:\w+>/<id:\d+>' => '<controller>/view',
                  '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                  '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
              ],
          ],

        /*'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
              //['class' => 'yii\rest\UrlRule', 'controller' => 'user-token'],
              '<controller:\w+>/<id:\d+>' => '<controller>/view',
              '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
              '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
              //['class' => 'yii\rest\UrlRule', 'controller' => 'item'],
            ],
        ],*/
    ],
    'params' => $params,
    'defaultRoute' => '/service/index',
];
