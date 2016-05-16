<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use common\models\Yiigun;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;

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
        //echo $m->id.'<br />';
        $raw_response = $yg->get($m->url);
        $response = $raw_response->http_response_body;
        var_dump ($response->http_response_body);

        exit;
        // parse the meeting id
        if (isset($response->To)) {
          $to_address = $response->To;
        } else {
          $to_address = $response->to;
        }
        $to_address = str_ireplace('@meetingplanner.io','',$to_address);
        $to_address = str_ireplace('mp_','',$to_address);
        $meeting_id = intval($to_address);
        // verify meeting id is valid
        if (isset($response->Sender)) {
          $sender = $response->Sender;
        } else {
          $sender = $response->sender;
        }
        $user_id = User::findByEmail($sender);
        if ($user_id===false) {
          // do nothing
          // to do - reply with do not recognize
        } else {
          // verify sender is a participant or organizer to this meeting
          $is_attendee = Meeting::isAttendee($meeting_id,$user_id);
          if ($is_attendee) {
            // add meeting note with log entry
            // to do - security clean post body
            // MeetingNote::add
            // update timestamp in aftersave probably already done
          } else {
            // do nothing
            // to do - reply with do not recognize
          }
        }
        // delete the message from the store
        $yg->delete($m->url);
        // mark as read
        $m->status = MailgunNotification::STATUS_READ;
        $m->update();
      }
    }
}
