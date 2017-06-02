<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\MiscHelpers;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\MeetingTime;
use frontend\models\MeetingPlace;
use frontend\models\RequestResponse;

/**
 * This is the model class for table "request".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $requestor_id
 * @property integer $completed_by
 * @property integer $time_adjustment
 * @property integer $alternate_time
 * @property integer $meeting_time_id
 * @property integer $place_adjustment
 * @property integer $meeting_place_id
 * @property string $note
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $requestor
 * @property Meeting $meeting
 */
class Request extends \yii\db\ActiveRecord
{
  const STATUS_OPEN = 0;
  const STATUS_ACCEPTED = 10;
  const STATUS_REJECTED = 20;
  const STATUS_WITHDRAWN = 30;

  const TIME_ADJUST_NONE = 50;
  const TIME_ADJUST_ABIT = 60;
  const TIME_ADJUST_OTHER = 70;

  const PLACE_ADJUST_NONE = 80;
  const PLACE_ADJUST_OTHER = 90;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'requestor_id','completed_by', 'time_adjustment', 'alternate_time', 'meeting_time_id', 'place_adjustment', 'meeting_place_id', 'status'], 'integer'],
            [['note'], 'string'],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'requestor_id' => Yii::t('frontend', 'Requested by'),
            'completed_by' => Yii::t('frontend', 'Completed by'),
            'time_adjustment' => Yii::t('frontend', 'Time Adjustment'),
            'alternate_time' => Yii::t('frontend', 'Number Seconds'),
            'meeting_time_id' => Yii::t('frontend', 'Meeting Time ID'),
            'place_adjustment' => Yii::t('frontend', 'Place Adjustment'),
            'meeting_place_id' => Yii::t('frontend', 'Meeting Place ID'),
            'note' => Yii::t('frontend', 'Note'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor()
    {
        return $this->hasOne(User::className(), ['id' => 'requestor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    public static function buildSubject($request_id,$include_requestor = true) {
      $r = Request::findOne($request_id);
      if (is_null($r)) {
        // sentry item 285101406
        // bug: meeting being accepted but this breaks
        return Yii::t('frontend','Accepted Requested Change to Meeting');
      }
      $requestor = MiscHelpers::getDisplayName($r->requestor_id);
      $timezone = MiscHelpers::fetchUserTimezone(Yii::$app->user->getId());
      $rtime ='';
      $place = '';
      switch ($r->time_adjustment) {
        case Request::TIME_ADJUST_NONE:
        break;
        case Request::TIME_ADJUST_ABIT:
          $rtime = Meeting::friendlyDateFromTimestamp($r->alternate_time,$timezone);
        break;
        case Request::TIME_ADJUST_OTHER:
          $t = MeetingTime::findOne($r->meeting_time_id);
          if (!is_null($t)) {
              $rtime = Meeting::friendlyDateFromTimestamp($t->start,$timezone);
          }
        break;
      }
      if ($r->place_adjustment == Request::PLACE_ADJUST_NONE || $r->place_adjustment == 0 && $r->meeting_place_id ==0 ) {
        // do nothing
      } else {
        // get place name
        $place = MeetingPlace::findOne($r->meeting_place_id)->place->name;
      }
      $result = $requestor.Yii::t('frontend',' asked to meet at ');
      if ($rtime=='' && $place =='') {
        $result.=Yii::t('frontend','oops...no changes were requested.');
      } else if ($rtime<>'') {
        $result.=$rtime;
        if ($place<>'') {
          $result.=Yii::t('frontend',' and ');
        }
      }
      if ($place<>'') {
        $result.=$place;
      }
      return $result;
    }

    public function accept($request_response) {
      // to do - this will need to change when there are multiple participants
      $this->status = Request::STATUS_ACCEPTED;
      $this->update();
      $m = Meeting::findOne($this->meeting_id);
      // is there a new time
      switch ($this->time_adjustment) {
        case Request::TIME_ADJUST_ABIT:
          // create a new meeting time with alternate_time
          $this->meeting_time_id = MeetingTime::addFromRequest($this->id);
          $this->update();
          // mark as selected
          MeetingTime::setChoice($this->meeting_id,$this->meeting_time_id,$request_response->responder_id);
        break;
        case Request::TIME_ADJUST_OTHER:
         // mark as selected
          MeetingTime::setChoice($this->meeting_id,$this->meeting_time_id,$request_response->responder_id);
        break;
      }
      // is there a different place
      if ($this->place_adjustment == Request::PLACE_ADJUST_OTHER || $this->meeting_place_id !=0 ) {
        MeetingPlace::setChoice($this->meeting_id,$this->meeting_place_id,$request_response->responder_id);
      }
      if ($m->isOwner($request_response->responder_id)) {
        // they are an organizer
        $this->completed_by =$request_response->responder_id;
        $this->update();
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_ORGANIZER_ACCEPT,$request_response->responder_id,$this->id);
      } else {
        // they are a participant
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_ACCEPT,$request_response->responder_id,$this->id);
      }
      $user_id = $request_response->responder_id;
      $subject = Request::buildSubject($this->id, true);
      $p1 = MiscHelpers::getDisplayName($user_id).Yii::t('frontend',' accepted the request: ').$subject;
      $p2 = $request_response->note;
      $p3 = Yii::t('frontend','You will receive an updated meeting confirmation reflecting these change(s). It will also include an updated attachment for your Calendar.');
      $content=[
        'subject' => Yii::t('frontend','Accepted Requested Change to Meeting'),
        'heading' => Yii::t('frontend','Requested Change Accepted'),
        'p1' => $p1,
        'p2' => $p2,
        'p3' => $p3,
        'plain_text' => $p1.' '.$p2.' '.$p3.'...'.Yii::t('frontend','View the meeting here: '),
      ];
      $button= [
        'text' => Yii::t('frontend','View the Meeting'),
        'command' => Meeting::COMMAND_VIEW,
        'obj_id' => 0,
      ];
      $this->notify($user_id,$this->meeting_id, $content,$button);
      // Make changes to the Meeting
      $m->increaseSequence();
      // resend the finalization - which also needs to be done for resend invitation
      $m->finalize($m->owner_id);
    }

    public function reject($request_response) {
      $this->status = Request::STATUS_REJECTED;
      $this->update();
      $m = Meeting::findOne($this->meeting_id);
      if ($m->isOwner($request_response->responder_id)) {
        // they are an organizer
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_ORGANIZER_REJECT,$request_response->responder_id,$this->id);
        $this->completed_by =$request_response->responder_id;
        $this->update();
      } else {
        // they are a participant
        MeetingLog::add($this->meeting_id,MeetingLog::ACTION_REQUEST_REJECT,$request_response->responder_id,$this->id);
      }
      $meeting_id = $this->meeting_id;
      $user_id = $request_response->responder_id;
      $subject = Request::buildSubject($this->id, false);
      $p1 = MiscHelpers::getDisplayName($user_id).Yii::t('frontend',' declined the request for ').$subject;
      $p2 = $request_response->note;
      $content=[
        'subject' => Yii::t('frontend','Declined Requested Change to Meeting'),
        'heading' => Yii::t('frontend','Declined Requested Change'),
        'p1' => $p1,
        'p2' => $p2,
        'plain_text' => $p1.' '.$p2.'...'.Yii::t('frontend','View the meeting here: '),
      ];
      $button= [
        'text' => Yii::t('frontend','View the Meeting'),
        'command' => Meeting::COMMAND_VIEW,
        'obj_id' => 0,
      ];
      // to do - consider if organizersOnly - and would need to build that in local notify()
      // note - or could migrate notify() here to Meeting::generic_notify
      $this->notify($user_id,$meeting_id, $content,$button);
    }

    public function opinion($opinion) {
      switch ($opinion) {
        case RequestResponse::RESPONSE_LIKE:
          $log_action = MeetingLog::ACTION_REQUEST_LIKE;
        break;
        case RequestResponse::RESPONSE_DISLIKE:
          $log_action = MeetingLog::ACTION_REQUEST_DISLIKE;
        break;
        case RequestResponse::RESPONSE_NEUTRAL:
          $log_action = MeetingLog::ACTION_REQUEST_NEUTRAL;
        break;
      }
      MeetingLog::add($this->meeting_id,$log_action,Yii::$app->user->getId(),$this->id);
    }

    public function withdraw($id) {
      // check that withdrawee created it
      $r = Request::findOne($id);
      $r->status = Request::STATUS_WITHDRAWN;
      $r->update();
      $user_id = $r->requestor_id;
      $meeting_id = $r->meeting_id;
      $subject = Request::buildSubject($id, false);
      $p1 = MiscHelpers::getDisplayName($r->requestor_id).Yii::t('frontend',' withdrew the request for ').$subject;
      $content=[
        'subject' => Yii::t('frontend','Requested Change Withdrawn'),
        'heading' => Yii::t('frontend','Requested Change Withdrawn'),
        'p1' => $p1,
        'p2' => '',
        'plain_text' => $p1.'...'.Yii::t('frontend','View the meeting here: '),
      ];
      $button= [
        'text' => Yii::t('frontend','View the Meeting'),
        'command' => Meeting::COMMAND_VIEW,
        'obj_id' => 0,
      ];
      $this->notify($user_id,$meeting_id, $content,$button);
    }

    public function create() {
      $user_id = $this->requestor_id;
      $meeting_id = $this->meeting_id;
      $subject = Request::buildSubject($this->id);
      $content=[
        'subject' => Yii::t('frontend','Change Requested to Your Meeting'),
        'heading' => Yii::t('frontend','Requested Change to Your Meeting'),
        'p1' => $subject,
        'p2' => $this->note,
        'plain_text' => $subject.' '.$this->note.'...'.Yii::t('frontend','Respond to the request by visiting this link: '),
      ];
      $button= [
        'text' => Yii::t('frontend','Respond to Request'),
        'command' => Meeting::COMMAND_VIEW_REQUEST,
        'obj_id' => $this->id,
      ];
      $this->notify($user_id,$meeting_id, $content,$button);
      // add to log
      MeetingLog::add($meeting_id,MeetingLog::ACTION_REQUEST_SENT,$user_id,0);
    }

    public static function notify($user_id,$meeting_id,$content,$button = false) {
      // sends a generic message based on arguments
      $mtg = Meeting::findOne($meeting_id);
      // build an attendees array for all participants without contact information
      $cnt =0;
      $attendees = [];
      foreach ($mtg->participants as $p) {
          $auth_key=\common\models\User::find()->where(['id'=>$p->participant_id])->one()->auth_key;
          $attendees[$cnt]=['user_id'=>$p->participant_id,'auth_key'=>$auth_key,
          'email'=>$p->participant->email,
          'username'=>$p->participant->username];
          $cnt+=1;
      }
      // add organizer
      $auth_key=\common\models\User::find()->where(['id'=>$mtg->owner_id])->one()->auth_key;
      $attendees[$cnt]=['user_id'=>$mtg->owner_id,'auth_key'=>$auth_key,
        'email'=>$mtg->owner->email,
        'username'=>$mtg->owner->username];
    // use this code to send
    foreach ($attendees as $cnt=>$a) {
      // check if email is okay and okay from this sender_id
      if ($user_id != $a['user_id'] && User::checkEmailDelivery($a['user_id'],$user_id)) {
        $priorLanguage=\Yii::$app->language;
        $language = UserSetting::getLanguage($a['user_id']);
        if ($language!==false) {
          \Yii::$app->language=$language;
        }
        Yii::$app->timeZone = $timezone = MiscHelpers::fetchUserTimezone($a['user_id']);
          // Build the absolute links to the meeting and commands
          $links=[
            'home'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'view'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_VIEW,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'footer_email'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'footer_block'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK,$user_id,$a['user_id'],$a['auth_key'],$mtg->site_id),
            'footer_block_all'=>MiscHelpers::buildCommand($mtg->id,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key'],$mtg->site_id),
          ];
          if ($button!==false) {
            $links['button_url']=MiscHelpers::buildCommand($mtg->id,$button['command'],$button['obj_id'],$a['user_id'],$a['auth_key'],$mtg->site_id);
            $content['button_text']=$button['text'];
          }
          // send the message
          $message = Yii::$app->mailer->compose([
            'html' => 'generic-html',
            'text' => 'generic-text'
          ],
          [
            'meeting_id' => $mtg->id,
            'sender_id'=> $user_id,
            'user_id' => $a['user_id'],
            'auth_key' => $a['auth_key'],
            'links' => $links,
            'content'=>$content,
            'meetingSettings' => $mtg->meetingSettings,
        ]);
          // to do - add full name
        $message->setFrom(['support@meetingplanner.io'=>$mtg->owner->email]);
        $message->setReplyTo('mp_'.$mtg->id.'@meetingplanner.io');
        $message->setTo($a['email'])
            ->setSubject($content['subject'])
            ->send();
        \Yii::$app->language=$priorLanguage;
        }
      }
    }

    public static function countRequestorOpen($meeting_id,$requestor_id) {
      return Request::find()->where(['meeting_id'=>$meeting_id,'requestor_id'=>$requestor_id,'status'=>Request::STATUS_OPEN])->count();
    }

    public static function countOpen($meeting_id) {
      return Request::find()->where(['meeting_id'=>$meeting_id,'status'=>Request::STATUS_OPEN])->count();
    }
    /**
     * @inheritdoc
     * @return RequestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RequestQuery(get_called_class());
    }

}
