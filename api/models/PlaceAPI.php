<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
namespace api\models;

use Yii;
use yii\base\Model;
use api\models\Service;
use common\models\User as U2;
use common\components\SiteHelper;
use common\components\MiscHelpers;
use frontend\models\Place;

class PlaceAPI extends Model
{
    public static function get($token,$place_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $place = Place::find()
        ->where(['id'=>$place_id])
        ->one();
      return $place;
    }
}
