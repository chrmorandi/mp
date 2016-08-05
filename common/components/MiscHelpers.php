<?php
namespace common\components;
use yii;
use yii\helpers\Url;
use common\models\User;

class MiscHelpers  {

  public static function buildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key='') {
    // to do - build string of local or remote destination
    // note: if change made, change in Message.php
    return Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,],true);
   }

   public static function backendBuildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key='') {
     // to do - build string of local or remote destination
     // note: if change made, change in Message.php
     $url = Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,]);
     $url = str_ireplace('/mpa/index.php/','',$url);
     return MiscHelpers::buildUrl().$url;
    }

    public static function buildUrl() {
      // manages links created from backend to go to frontend
       $baseUrl = Url::home(true);
       if (stristr($baseUrl,'localhost')===false) {
         // live site
         $url = 'https://meetingplanner.io';
       } else {
          // development - returns http://localhost:8888/mpa/index.php
          // change to front end
          $url = 'http://localhost:8888/mp/';
       }
      // messages are sent from back end but need to link to front end url
      return $url;
    }

   public static function isProfileEmpty($user_id) {
     // returns false or userprofile id
     $profileEmpty=false;
     $profile = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
     if (is_null($profile)) {
       $up_id = \frontend\models\UserProfile::initialize($user_id);
       $profileEmpty = $up_id;
     } else if (empty($profile->firstname) && empty($profile->lastname) && ($profile->fullname==' ' || $profile->fullname=='')) {
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
       if ($profile->fullname<>'' and $profile->fullname<>' ') {
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

  public static function getUrlPrefix() {
    // to do - obviate this with proper config of base url
    return (isset(Yii::$app->params['urlPrefix'])? $urlPrefix = Yii::$app->params['urlPrefix'] : '');
  }

  public static function br($n = 1) {
    $str = '';
    for ($i=0;$i<$n;$i++) {
      $str.='<br />';
    }
    return $str;
  }

  public static function downloadFile($fullpath){
    $fullpath = str_replace ( './invites' ,'invites', $fullpath);
    $fullpath = '/var/www/mp/frontend/web/'.$fullpath;
    echo $fullpath;exit;
    if(!empty($fullpath)){
        //header("Content-type:application/pdf"); //for pdf file
        header('Content-Type:text/plain; charset=ISO-8859-15');
        header('Content-Disposition: attachment; filename="'.basename($fullpath).'"');
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);
        Yii::app()->end();
    }
  }
}
?>
