<?php

namespace api\models;

use Yii;
use yii\base\Model;
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
    public static function verifyAccess($app_id,$app_secret) {
      if ($app_id == Yii::$app->params['app_id']
        && $app_secret == Yii::$app->params['app_secret']) {
            Yii::$app->params['site']['id']=SiteHelper::SITE_SP;
            return true;
        } else {
          return false;
        }
      }

}
