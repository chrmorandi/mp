<?php
$config = parse_ini_file('/var/secure/mp.ini', true);

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'mp-frontend',
    'name' => 'Meeting Planner',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
      'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => false,
            'rules' => [
              'place' => 'place',
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
            ],
        ],
      'authClientCollection' => [
              'class' => 'yii\authclient\Collection',
              'clients' => [
                  'facebook' => [
                      'class' => 'yii\authclient\clients\Facebook',
                      'clientId' => $config['oauth_fb_id'],
                      'clientSecret' => $config['oauth_fb_secret'],
                  ],
                  'google' => [
                      'class' => 'yii\authclient\clients\GoogleOAuth',
                      'clientId' => $config['oauth_google_client_id'],
                      'clientSecret' => $config['oauth_google_client_secret'],
                    ],
                  'linkedin' => [
                      'class' => 'yii\authclient\clients\LinkedIn',
                      'clientId' => $config['linkedin_client_id'],
                      'clientSecret' => $config['linkedin_client_secret'],
                  ],
                  /*'twitter' => [
                      'class' => 'yii\authclient\clients\Twitter',
                      'consumerKey' => $config['oauth_twitter_key'],
                      'consumerSecret' => $config['oauth_twitter_secret'],
                              ],*/
              ],
          ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'Yii2Twilio' => [
          'class' => 'filipajdacic\yiitwilio\YiiTwilio',
          'account_sid' => $config['twilio_sid'],
          'auth_key' => $config['twilio_token'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
    'defaultRoute' => '/site/index',
];
