<?php

namespace api\models;

use Yii;
use yii\base\Model;
use common\models\User;
use api\models\UserToken;
use frontend\models\Meeting;
use frontend\models\MeetingPlaceChoice;
use frontend\models\MeetingTimeChoice;
use frontend\models\MeetingLog;
use frontend\models\MeetingNote;

class MeetingAPI extends Model
{
    public static function meetinglist($token,$user_id) {
      $user_id = UserToken::lookup($token);
      if (!$user_id) {
        return Service::fail('invalid token');
      }
      $meetings = Meeting::find()
        ->joinWith('participants')
        ->where(['owner_id'=>$user_id])
        ->orWhere(['participant_id'=>$user_id])
        ->andWhere(['meeting.status'=>[Meeting::STATUS_PLANNING,Meeting::STATUS_SENT]])
        ->distinct()
        ->orderBy(['created_at'=>SORT_DESC])
        ->all();
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
      $mpc = MeetingPlaceChoice::find()
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
      $mtc = MeetingTimeChoice::find()
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
}
