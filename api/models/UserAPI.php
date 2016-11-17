<?php

namespace api\models;

use Yii;
use yii\base\Model;
use api\models\Service;
use common\models\User as U2;
use common\components\SiteHelper;
use common\components\MiscHelpers;

class UserAPI extends Model
{
    public static function timezone($token) {
        $user_id = UserToken::lookup($token);
        if (!$user_id) {
          return Service::fail('invalid token');
        }
        return MiscHelpers::fetchUserTimezone($user_id);
    }
}
