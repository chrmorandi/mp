<?php

namespace api\models;

use Yii;
use yii\base\Model;
use api\models\Service;
use common\models\User as U2;
use common\components\SiteHelper;
use common\components\MiscHelpers;
use frontend\models\Friend;
use frontend\models\Place;
use frontend\models\UserContact;
use frontend\models\UserPlace;

class UserAPI extends Model
{
    public static function timezone($token) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        return MiscHelpers::fetchUserTimezone($user_id);
    }

    public static function friends($token) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        return Friend::getDetailedFriendList($user_id);
    }

    public static function fullname($token,$name_id) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        return MiscHelpers::getDisplayName($name_id);
    }

    public static function contacts($token) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        return UserContact::getUserContactList($user_id);
    }

    public static function places($token) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        $places = UserPlace::find()
        ->where(['user_id'=>$user_id])
        ->all();
        $result = [];
        foreach ($places as $p) {
          $x = new \stdClass();
          $x->place_id = $p->place_id;
          $x->name = Place::findOne($p->place_id)->name;
          $result[]= $x;
          unset($x);
        }
        return $result;
    }

}
