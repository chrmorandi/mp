<?php
// this model provides cleanup for legacy issues as code evolves

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use frontend\models\Friend;
use frontend\models\Reminder;
use frontend\models\UserSetting;
use backend\models\UserData;
use frontend\models\MeetingReminder;

class Fix
{

  public static function checkUserDataCalc() {
    $since = false;
    if ($since===false) {
      $since = mktime(0, 0, 0);
    }
    $after = mktime(0, 0, 0, 2, 15, 2016);
    $monthago = $since-(60*60*24*30);
    $all = User::find()->where('created_at>'.$after)->andWhere('created_at<'.$since)->all();
    foreach ($all as $u) {
      echo $u->id.'<br />';
      // create new record for user or update old one
      $ud = UserData::find()->where(['user_id'=>$u->id])->one();
      if (is_null($ud)) {
        echo $u->email.'<br />';
      }
    }
  }

  public static function fixUserSettings() {
    // task resets default user settings for everyone
      $all = UserSetting::find()->all();
      foreach ($all as $us) {
        $us->participant_add_activity=$us->participant_add_place;
        $us->participant_choose_activity=$us->participant_choose_place;
        $us->update();
      }
  }

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

  public static function cleanupEmails() {
    $badEmails=[
      '',
'test2@gmail.com',
'1111@gmail.com',
'qwerty@gmail.com',
'amjadiqbalkhanniazi@gmail.com',
'admin@admin.com',
'rhizalpatra@fellow.lpkia.ac.id',
'tm@archi.com',
'test@test.com',
'web@yahoo.fr',
'a@a.a',
'ailaa@aa.com',
'be@yahoo.fr',
'vico@gmail.com',
'nobu@gmail.com',
'a@gmail.com',
'ct@gmail.com',
'sanjaydk@projectdemo.biz',
'trial@gmail.com',
'varlog255q@hotmail.com',
'baah@baah.com',
'minhvnn1@gmail.com',
'test@gmail.com',
'test@mediabite.co.uk',
'ddd@c.hu',
'ddd@ymail.com',
'a.chetan@saisoftex.com',
'user02@local.com',
'Imrky4@gmail.com',
'robomadybu@hotmail.com',
'mike@mike.mike',
'abcd@gmail.com',
'azazaz@azazaza.com',
'mama@mama.mn',
'qweqwe@qwe.qwe',
'testere@wp.pl',
'kaze@hotmail.com',
'test@usertest.fr',
'demodemo@demo.com',
'qqq@dd.gh',
'gnfbb@h.vo',
'admin@admin123.com',
'testsir@testsir.com',
'oi.hd@yeah1.vn',
'loi.hd@yeah1.vn',
'test@email.com',
'salom@salom.com',
'ar@yahoo.com',
'lex@gmail.com',
'Tester1234@gmail.com',
'mantaf@mail.com',
'aaa@aaa.com',
'oeui@gmail.com',
'risitesh.biswal14@yahoo.com',
'ttt@wp.pl',
'nnn@nnn.net',
'nnn2@nnn.net',
'ana@gmail.com',
'asdf@yahoo.com',
'noom@gmail.com',
'jomon@example.com',
'asdfasdf@yahoo.com',
'admin@yahoo.com',
'abinubli@mail.com',
'tes@tes.com',
'asdasdr@asd.com',
'something@some.com',
'ademin@example.com',
'd@dd.com',
'robo@gmail.com',
'toto@titi.com',
'fesfe@fseff.fes',
'master@wpthemeslist.com',
'teste@teste.com',
'barny182@hotmail.com',
'test@admin.com',
'billtian@test.com',
'Test@goggle.ca',
'jm@gmail.com',
'john-panin@qip.ru',
'loslos@loslos.com',
'ghfhf@jhgjgjk.com',
'lol@lol.com',
'tester1@gmail.com',
'g0952180828@gmail.com',
'testim@testim.com',
'mnml.name@gmail.com',
'endri.azizi.92@gmail.com',
'123123@gmail.com',
'myfriend@gmai.com',
'geraldo_1989@hotmail.com',
'rob.test.999@gmail.com',
'j@c.com',
'Agung.andika@mhs.uinjkt.ac.id',
'W3test@ya.ru',
'user@ya.ru',
'ed@ed.fl',
'ed@ed.es',
    ];
    foreach ($badEmails as $e) {
      $u = User::find()->where(['email'=>$e])->one();
      if (is_null($u)) {
        echo 'no action '.$e.'<br />';
        continue;
      } else {
        echo 'deleting '.$e.'<br />';
        $u->status = User::STATUS_DELETED;
        $u->update();
        var_dump(User::checkEmailDelivery($u->id));
      }
    }
  }
}
?>
