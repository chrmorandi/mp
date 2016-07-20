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
            'enableSession' => true,
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
];
