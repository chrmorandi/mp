<?php

namespace common\components;
use \yii;
use \yii\authclient\clients\Twitter;
use \yii\authclient\OAuthToken;

class SocialHelpers  {

    public static function fetchEmail() {
      // create your OAuthToken
      $token = new OAuthToken([
          'token' => Yii::$app->params['twitterAccessToken'],
          'tokenSecret' => Yii::$app->params['twitterAccessTokenSecret']
      ]);
      // start a Twitter Client and configure your access token with your
      // recently created token
      $twitter = new Twitter([
          'accessToken' => $token,
          'consumerKey' => Yii::$app->params['twitterApiKey'],
          'consumerSecret' => Yii::$app->params['twitterApiSecret'],
      ]);

      $result = $twitter->api('account/verify_credentials.json', 'GET',[ "skip_status"=>"true","include_email" => "true"]);
      if (isset($result['email']))
        return $result['email'];
      else {
        return '';
      }
    }
}


?>
