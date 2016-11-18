<?php

namespace api\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\components\MiscHelpers;
use api\models\UserToken;
use frontend\models\Meeting;
use frontend\models\MeetingLog;
use frontend\models\MeetingSetting;
use frontend\models\MeetingNote;

class MeetingAPI extends Model
{
    public static function meetinglist($token,$status) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      if ($status == Meeting::STATUS_PLANNING || $status == Meeting::STATUS_SENT) {
        $queryStatus =[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT];
      } else {
        $queryStatus = $status;
      }
      // get calling user's timezone
      $timezone = MiscHelpers::fetchUserTimezone($user_id);
      $meeting_list = Meeting::find()
        ->joinWith('participants')
        ->where(['owner_id'=>$user_id])
        ->orWhere(['participant_id'=>$user_id])
        ->andWhere(['meeting.status'=>$queryStatus])
        ->distinct()
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      $meetings=[];
      foreach ($meeting_list as $m) {
        $x = new \stdClass();
        $x->id = $m->id;
        $x->owner_id= $m->owner_id;
        $x->meeting_type = $m->meeting_type ;
        $x->subject = $m->subject ;
        $x->message = $m->message ;
        $x->identifier = $m->identifier ;
        $x->status = $m->status ;
        $x->created_at = $m->created_at ;
        $x->logged_at = $m->logged_at ;
        $x->sequence_id = $m->sequence_id ;
        $x->cleared_at = $m->cleared_at;
        $x->site_id = $m->site_id ;
        if ($status >= Meeting::STATUS_CONFIRMED) {
          $x->chosenTime=Meeting::getChosenTime($m->id);
          $x->caption = $m->friendlyDateFromTimestamp($x->chosenTime->start,$timezone,true,true).' '.$m->getMeetingParticipants();
          $x->chosenPlace = Meeting::getChosenPlace($m->id);
          if ($x->chosenPlace!==false) {
            $x->place = $x->chosenPlace->place;
            $x->gps = $x->chosenPlace->place->getLocation($x->chosenPlace->place->id);
            $x->noPlace = false;
          } else {
            $x->place = false;
            $x->noPlace = true;
            $x->gps = false;
          }
        } else {
          $x->chosenTime=0;
          $x->chosenPlace = 0;
          $x->caption = $m->getMeetingParticipants();
        }

        //$x->extra = 'apple'.$m->id;
        /*
        $x-> = $m-> ;
        $x-> = $m-> ;
        $x-> = $m-> ;
        */
        $meetings[]=$x;
        unset($x);
      }
      return $meetings;
    }

    public static function history($token,$meeting_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      // check user is a participant
      if (!Meeting::isAttendee($meeting_id,$user_id)) {
        return Service::fail('token holder is not a meeting participant');
      }
      $logObj = new \stdClass();
      $logs = MeetingLog::find()
        ->where(['meeting_id'=>$meeting_id])
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
        return $logs;
    }

    public static function meetingplacechoices($token,$meeting_place_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $mpc = \frontend\models\MeetingPlaceChoice::find()
        ->where(['user_id'=>$user_id])
        ->andWhere(['meeting_place_id'=>$meeting_place_id])
        ->all();
      return $mpc;
    }

    public static function meetingtimechoices($token,$meeting_time_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $mtc = \frontend\models\MeetingTimeChoice::find()
        ->where(['user_id'=>$user_id])
        ->andWhere(['meeting_time_id'=>$meeting_time_id])
        ->all();
      return $mtc;
    }

    public static function notes($token,$meeting_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $notes = MeetingNote::find()
        ->where(['meeting_id'=>$meeting_id])
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
      return $notes;
    }

    public static function settings($token,$meeting_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $settings = MeetingSetting::find()
        ->where(['meeting_id'=>$meeting_id])
        ->all();
      return $settings;
    }

    public static function details($token,$meeting_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $result = new \stdClass();
      $m= Meeting::findOne($meeting_id);
      // user_id is viewer_id
      if (count($m->meetingTimes)>0) {
          $result->times = \frontend\models\MeetingTime::getWhenStatus($m,$user_id);
      }
      if (count($m->meetingPlaces)>0) {
        $result->places = \frontend\models\MeetingPlace::getWhereStatus($m,$user_id);
      }
      return $result;
    }
}
