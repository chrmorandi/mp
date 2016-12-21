<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
namespace api\models;

use Yii;
use yii\base\Model;
use api\models\UserToken;
use common\models\User;
use common\components\SiteHelper;
/**
 * This is the model class for table "user_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Service extends Model
{

  /**
   * Verifies an argument list and signature for a user call
   *
   * @property string $signature
   * @property integer $user_id
   * @property string $arg_str
   */
  public static function verifySignature($signature,$user_id,$arg_str) {
    // lookup token from user_id
    $ut = UserToken::find()
      ->where(['user_id'=>$user_id])
      ->one();
    if (is_null($ut)) {
      // error
      return false;
    } else {
      // generate a hash with user's token and compare to the $signature
      $gen_sig = hash_hmac('sha256',$arg_str,$ut->token);
      if ($signature == $gen_sig) {
        return true;
      }
    }
    return false;
  }

    public static function verifyAccess($app_id,$app_secret) {
      if ($app_id == Yii::$app->params['app_id']
        && $app_secret == Yii::$app->params['app_secret']) {
            Yii::$app->params['site']['id']=SiteHelper::SITE_SP;
            return true;
        } else {
          return false;
        }
      }

    public static function fail($message ='') {
      $obj = new \stdClass();
      $obj->result = 'failure';
      $obj->message = $message;
      return $obj;
    }
}
