<?php
namespace common\components;
use yii;
use yii\helpers\Url;
use common\models\User;

class MiscHelpers  {
  public static function buildUrl() {
    // manages links created from backend to go to frontend
    return Yii::$app->params['site']['url'];
    /*
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
    */
  }

  public static function getSiteUrl($site_id = 0) {
      $baseUrl = Url::home(true);
      switch ($site_id) {
        case 0: // mp
        if (stristr($baseUrl,'/mp/')!==false) {
          // dev mp
          $url = 'http://localhost:8888';
        } else {
          $url = 'https://meetingplanner.io';
        }
        break;
        case 1: // sp
        if (stristr($baseUrl,'/sp/')!==false) {
         // dev sp
         $url = 'http://localhost:8888';
        } else {
          $url = 'https://simpleplanner.io';
        }
        break;
        case 2: // fd
        if (stristr($baseUrl,'/fd/')!==false) {
         // dev sp
         $url = 'http://localhost:8888';
       } else {
         // to do - change with fd launch
         $url = 'https://meetingplanner.io';
       }
        break;
      }
      return $url;
    }

  public static function buildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key='',$site_id = 0) {
    // to do - build string of local or remote destination
    // note: if change made, change in Message.php
    $baseUrl = MiscHelpers::getSiteUrl($site_id);
    $qs = Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,]);
    return $baseUrl.$qs;
   }

   public static function backendBuildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key='') {
     // to do - build string of local or remote destination
     // note: if change made, change in Message.php
     $url = Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,]);
     $url = str_ireplace('/mpa/','',$url);
     return MiscHelpers::buildUrl().$url;
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
     if (!$no_email && isset($u->email))
     {
       $displayName = $u->email;
     } else if (isset($u->username)) {
       $displayName = $u->username;
     } else {
       $displayName = 'Name unavailable';
     }
     $profile = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
     if (!is_null($profile)) {
       $calcName = $profile->firstname.' '.$profile->lastname;
       if ($profile->fullname!='' && $profile->fullname!=' ') {
         $displayName = $profile->fullname;
       } else if ($calcName<>' ') {
         // note check for middle space
         $displayName = $calcName;
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
    $zones_array = [];
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
    // to do - make this work in dev env and be configurable
    $baseUrl = Url::home(true);
    if (stristr($baseUrl,'localhost')===false) {
      $fullpath = '/var/www/mp/frontend/web/'.$fullpath;
    }
     if(!empty($fullpath)){
        //header("Content-type:application/pdf"); //for pdf file
        header('Content-Type: text/Calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="ical.ics";'); // '.basename($fullpath).'
        /*header("Pragma: public");
      	header("Expires: 0");
      	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      	header("Cache-Control: public");
      	header("Content-Description: File Transfer");
      	header("Content-type: application/octet-stream");
      	header("Content-Disposition: attachment; filename=\"invite.ics\"");
      	header("Content-Transfer-Encoding: binary");*/
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);
        Yii::$app->end();
    }
  }

  public static function listNames($items,$everyoneElse=false,$total_count=0,$anyoneElse=false) {
    $temp ='';
    $x=1;
    $cnt = count($items);
    if ($everyoneElse && $cnt >= ($total_count-1)) {
      if (!$anyoneElse) {
          $temp = Yii::t('frontend','everyone else');
      } else {
        $temp = Yii::t('frontend','anyone else');
      }
    } else {
      foreach ($items as $i) {
          $temp.= MiscHelpers::getDisplayName($i);
          if ($x == ($cnt-1)) {
            $temp.=' and ';
          } else if ($x < ($cnt-1)) {
            $temp.=', ';
          }
          $x+=1;
      }
    }
    return $temp;
  }

  public static function isIphone() {
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    return (strpos($user_agent, 'iPhone') !== FALSE || strpos($user_agent, 'iPad') !== FALSE);
  }
}
?>
