<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
use yii\db\ActiveRecord;
use common\models\Yiigun;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use frontend\models\MeetingNote;

/**
 * This is the model class for table "mailgun_notification".
 *
 * @property integer $id
 * @property string $url
 * @property integer $created_at
 * @property integer $updated_at
 */
class MailgunNotification extends \yii\db\ActiveRecord
{
  const STATUS_PENDING = 0;
  const STATUS_READ = 1;
  const STATUS_ERROR = 2;
  const STATUS_NOT_FOUND = 3;
  const STATUS_INVALID_MEETING_ID = 5;
  const STATUS_UNRECOGNIZED_SENDER = 10;
  const STATUS_NOT_ATTENDEE = 12;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailgun_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url',], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['url'], 'string', 'max' => 255],
        ];
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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'url' => Yii::t('frontend', 'Url'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function store($message_url) {
      // store the url from mailgun notification
        $mn = new MailgunNotification();
        $mn->status = MailgunNotification::STATUS_PENDING;
        $temp = str_ireplace('https://api.mailgun.net/v2/','',$message_url);
        $temp = str_ireplace('https://api.mailgun.net/v3/','',$temp);
        $mn->url = $temp;
        $mn->save();
    }

    public static function process() {
      $items = MailgunNotification::find()->where(['status'=>MailgunNotification::STATUS_PENDING])->all();
      if (count($items)==0) {
        return false;
      }
      $yg = new Yiigun();
      foreach ($items as $m) {
        $error = false;
        // echo $m->id.'<br />';
        // to do - error handling here
        $raw_response = $yg->get($m->url);
        if (is_null($raw_response)) {
          $m->status = MailgunNotification::STATUS_NOT_FOUND;
          $m->update();
          continue;
        }
        $response = $raw_response->http_response_body;
        //print_r($response);
        $stripped_text = \yii\helpers\HtmlPurifier::process($response->{'stripped-text'});
        // parse the meeting id
        if (isset($response->To)) {
          $to_address = $response->To;
        } else {
          $to_address = $response->to;
        }
        $to_address = str_ireplace('@meetingplanner.io','',$to_address);
        $to_address = str_ireplace('mp_','',$to_address);
        $meeting_id = (int)$to_address;
        if (!is_numeric($meeting_id)) {
          $error = true;
          $m->status = MailgunNotification::STATUS_INVALID_MEETING_ID;
          $m->update();
          continue;
        }
        // echo 'mid: '.$meeting_id.'<br>';
        // verify meeting id is valid
        if (isset($response->Sender)) {
          $sender = $response->Sender;
        } else {
          $sender = $response->sender;
        }
        // clean sender
        // echo ' pre clean sender: '.$sender.'<br>';
        $sender = \yii\helpers\HtmlPurifier::process($sender);
        // echo 'sender: '.$sender.'<br>';
        $u = User::findByEmail($sender);
        if ($u===false) {
          $error = true;
          // do nothing
          // to do - reply with do not recognize email address
          $m->status = MailgunNotification::STATUS_UNRECOGNIZED_SENDER;
          $m->update();
          continue;
        } else {
          $user_id = $u->id;
          // echo 'check attendee';
          // verify sender is a participant or organizer to this meeting
          $is_attendee = Meeting::isAttendee($meeting_id,$user_id);
          if ($is_attendee) {
            // add meeting note, automatically its logged and meeting touch stamp updated
            MeetingNote::add($meeting_id,$user_id,$stripped_text);
          } else {
            // do nothing
            // // echo 'not attendee';
            $error = true;
            // to do - reply with not an attendee of this meeting
            $m->status = MailgunNotification::STATUS_NOT_ATTENDEE;
            $m->update();
            continue;
          }
        }
        // delete the message from the store
        // to do turn store delete back on
        //$yg->delete($m->url);
        if (!$error) {
          // mark as read
          $m->status = MailgunNotification::STATUS_READ;
        } else {
          // mark as read
          $m->status = MailgunNotification::STATUS_ERROR;
        }
        $m->update();
      }
    }
}
