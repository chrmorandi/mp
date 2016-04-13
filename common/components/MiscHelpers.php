<?php
namespace common\components;
use yii\helpers\Url;
use common\models\User;

//use \yii\helpers\FormatConverter;

class MiscHelpers  {

  public static function buildCommand($meeting_id,$cmd=0,$obj_id=0,$actor_id=0,$auth_key) {
    return Url::to(['meeting/command','id'=>$meeting_id,'cmd'=>$cmd,'actor_id'=>$actor_id,'k'=>$auth_key,'obj_id'=>$obj_id,],true);
   }
 }
?>
