<?php
namespace common\components;
use yii\helpers\Url;
use common\models\User;

//use \yii\helpers\FormatConverter;

class MiscHelpers  {

  public static function buildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key='') {
    return Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,],true);
   }

   public static function isProfileEmpty($user_id) {
     // returns false or userprofile id
     $profileEmpty=false;
     $profile = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
     if (is_null($profile)) {
       $up_id = \frontend\models\UserProfile::initialize($user_id);
       $profileEmpty = $up_id;
     } else if (empty($profile->fullname)) {
       $profileEmpty=$profile->id;
     }
     return $profileEmpty;
   }

   public static function getDisplayName($user_id) {
     // returns best display name
     $displayName ='';
     $u = User::findOne($user_id);
     $profile = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
     if (is_null($profile)) {
       $displayName = $u->email;
     } else {
       $calcName = $profile->firstname.' '.$profile->lastname;
       if ($profile->fullname<>'') {
         $displayName = $profile->fullname;
       } else if ($calcName<>' ') {
         // note check for middle space
         $displayName = $calcName;
       } else {
         // profile names are Empty
         $displayName = $u->email;
       }
     }
     return $displayName;
   }
 }
?>
