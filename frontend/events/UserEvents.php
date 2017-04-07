<?php
namespace frontend\events;
use common\models\User;
class UserEvents {

    public static function handleAfterLogin()
    {
        $user=User::findOne(\Yii::$app->user->getId());
        $user::afterLogin();
    }
  }
?>
