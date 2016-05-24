<?php
// this model provides cleanup for legacy issues as code evolves

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use frontend\models\Friend;
use frontend\models\Reminder;
use frontend\models\MeetingReminder;

class Fix
{

  public static function fixPreFriends() {
    // before the friend feature worked
    // need to patch relationships
    $meetings = \frontend\models\Meeting::find()->all();
    foreach ($meetings as $m) {
      foreach ($m->participants as $p) {
        // add as friend - anyone people invited
        Friend::add($p->invited_by,$p->participant_id);
        if ($m->status >= \frontend\models\Meeting::STATUS_CONFIRMED) {
          // if meeting confirmed, add the converse
          Friend::add($p->participant_id,$p->invited_by);
        }
      }
    }
  }

  public static function fixPreReminders() {
    // legacy users before the new reminder model
    // need default reminders created
    $users = User::find()->all();
    foreach ($users as $u) {
      $rems = Reminder::find()->where(['user_id'=>$u->id])->all();
      if (is_null($rems) || count($rems)==0) {
        Reminder::initialize($u->id);
        $rems = Reminder::find()->where(['user_id'=>$u->id])->all();
      }
      foreach ($rems as $r) {
        Reminder::processNewReminder($r->id);
      }
    }
  }

  public static function cleanupReminders() {
    // erase all MeetingReminder
    MeetingReminder::deleteAll();
    // erase all reminders
    Reminder::deleteAll();
    Fix::fixPreReminders();
  }
}
?>
