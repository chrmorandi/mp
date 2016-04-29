<?php

namespace common\components;
use \yii;
use \yii\authclient\clients\Twitter;
use \yii\authclient\OAuthToken;

class SocialHelpers  {

    public static function fetchEmail($token,$tokenSecret) {
      // create your OAuthToken
      $token = new OAuthToken([
          'token' => Yii::$app->params['twitterAccessToken'],
          'tokenSecret' => Yii::$app->params['twitterAccessTokenSecret']
      //    'token' => $token,
      //    'tokenSecret' => $tokenSecret,
      ]);
      // start a Twitter Client and configure your access token with your
      // recently created token
      $twitter = new Twitter([
          'accessToken' => $token,
          'consumerKey' => Yii::$app->params['twitterApiKey'],
          'consumerSecret' => Yii::$app->params['twitterApiSecret'],
      ]);
      $result = $twitter->api('/access_token.json', 'POST',[ "oauth_verified"=>$tokenSecret]);
      var_dump($result);
      exit;
      $newToken = new OAuthToken([
          'token' => $token,
          'tokenSecret' => $result,
      ]);
      $twitter = new Twitter([
          'accessToken' => $newToken,
          'consumerKey' => Yii::$app->params['twitterApiKey'],
          'consumerSecret' => Yii::$app->params['twitterApiSecret'],
      ]);
      $result = $twitter->api('account/verify_credentials.json', 'GET',[ "skip_status"=>"true","include_email" => "true"]);
      var_dump($result);
      if (isset($result['email']))
        return $result['email'];
      else {
        return '';
      }
    }
}


?>
