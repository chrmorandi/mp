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

   public static function getDisplayName($user_id,$no_email=false) {
     // returns best display name
     $displayName ='';
     $u = User::findOne($user_id);
     $profile = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
     if (is_null($profile)) {
       if (!$no_email)
       {
         $displayName = $u->email;
       } else {
         $displayName = 'n/a';
       }
     } else {
       $calcName = $profile->firstname.' '.$profile->lastname;
       if ($profile->fullname<>'') {
         $displayName = $profile->fullname;
       } else if ($calcName<>' ') {
         // note check for middle space
         $displayName = $calcName;
       } else {
         // profile names are Empty
         if (!$no_email)
         {
           $displayName = $u->email;
         } else {
           $displayName = 'n/a';
         }
       }
     }
     return $displayName;
   }

   /**
 * Timezones list with GMT offset
 *
 * @return array
 * @link customized from http://stackoverflow.com/a/9328760
 */
public static function getTimezoneList() {
  $zones_array = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
    $zones_array[$zone] = $zone.' UTC/GMT ' . date('P', $timestamp);
  }
  return $zones_array;
}

public static function fetchUserTimezone($user_id) {
  // fetch user timezone
  $user_setting = \frontend\models\UserSetting::safeGet($user_id);
  if (!is_null($user_setting)) {
    $timezone = $user_setting->timezone;
  } else {
    $timezone = 'America/Los_Angeles';
  }
  return $timezone;
}

 }
?>
