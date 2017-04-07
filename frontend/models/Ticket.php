<?php
namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use common\models\User;
use common\components\MiscHelpers;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property integer $id
 * @property string $posted_by
 * @property string $email
 * @property string $subject
 * @property string $details
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property TicketReply[] $ticketReplies
 */
class Ticket extends \yii\db\ActiveRecord
{
  const STATUS_OPEN = 10;
  const STATUS_PENDING = 20;
  const STATUS_PENDING_USER = 25;
  const STATUS_CLOSED = 30;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ticket}}';
    }

    public function behaviors()
    {
        return [
            /*[
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'immutable' => true,
                'ensureUnique'=>true,
            ],*/
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
            [['posted_by', 'subject','details'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['posted_by','email','details','subject'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'posted_by' => Yii::t('frontend', 'Posted By'),
            'email' => Yii::t('frontend', 'Email'),
            'subject' => Yii::t('frontend', 'subject'),
            'details' => Yii::t('frontend', 'details'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketReplies()
    {
        return $this->hasMany(TicketReply::className(), ['ticket_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketQuery(get_called_class());
    }

    public static function getGuestId() {
      if (Yii::$app->user->isGuest) {
        $session = Yii::$app->session;
        $session->open();
        if (!$session->has('guest_id')) {
          $session->set('guest_id', Yii::$app->security->generateRandomString(32));
        }
        $guest_id = $session->get('guest_id');
      } else {
        $guest_id =Yii::$app->user->getId();
      }
      return $guest_id;
    }

    public static function setGuestId($guest_id) {
      // if viewing a ticket from a link, set the session again
      if (Yii::$app->user->isGuest) {
        $session = Yii::$app->session;
        $session->open();
        $session->set('guest_id', $guest_id);
      }
    }

    public function getStatus() {
      switch ($this->status) {
        case Ticket::STATUS_OPEN:
          return Yii::t('frontend','Awaiting staff response');
        break;
        case Ticket::STATUS_PENDING:
          return Yii::t('frontend','Awaiting staff response');
        break;
        case Ticket::STATUS_PENDING_USER:
          return Yii::t('frontend','Awaiting your response');
        break;
        case Ticket::STATUS_CLOSED:
          return Yii::t('frontend','Closed');
        break;
      }
    }

    //Ticket::deliver('new',$model->id,$model->posted_by,$model->email,$model->subject,$model->details);
    public static function deliver($mode='',$ticket_id,$recipient_id,$email='',$subject='',$details='') {
      $priorLanguage=\Yii::$app->language;
      $u=User::findOne($recipient_id);
      if (isset($u)) {
        $auth_key = $u->auth_key;
        $links=[
          'home'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,$u->auth_key,0),
          'view'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_VIEW_TICKET,$ticket_id,$recipient_id,$u->auth_key,0),
          'footer_email'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_EMAIL,0,$recipient_id,$u->auth_key,0),
          'footer_block'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK,$u->id,$recipient_id,$u->auth_key,0),
          'footer_block_all'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$recipient_id,$u->auth_key,0),
        ];
        $language = UserSetting::getLanguage($recipient_id);
        if ($language!==false) {
          \Yii::$app->language=$language;
        }
      } else {
        // posted_by is for a guest ticket
        $auth_key=0;
        $links=[
          'home'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,0,0),
          'view'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_VIEW_TICKET,$ticket_id,$recipient_id,0,0),
          'footer_email'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,0,0),
          'footer_block'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,0,0),
          'footer_block_all'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,0,0),
        ];
      }
      $link_url=$links['view'];
      switch ($mode) {
        case 'new':
          $content=[
            'subject' => Yii::t('frontend','Receipt acknowledgement: {subject}',['subject'=>$subject]),
            'heading' => Yii::t('frontend','Your Support Request'),
            'p1' => $subject,
            'p2' => Yii::t('frontend','We will notify you when we have responded.'),
            'plain_text' => $subject.', '.$details.'...'.Html::a(Yii::t('frontend','View your ticket'),$link_url)
          ];
        break;
        case 'reply':
          $content=[
            'subject' => Yii::t('frontend','{subject}',['subject'=>$subject]),
            'heading' => Yii::t('frontend','Reply to Your Support Request'),
            'p1' => $details,
            'p2' => '',
            'plain_text' => $details.'...'.Html::a(Yii::t('frontend','View your ticket'),$link_url)
          ];
        break;
        case 'update':
          $content=[
            'subject' => Yii::t('frontend','Receipt acknowledgement: {subject}',['subject'=>$subject]),
            'heading' => Yii::t('frontend','Your Support Request'),
            'p1' => $subject,
            'p2' => Yii::t('frontend','We will notify you when we have responded.'),
            'plain_text' => $subject.', '.$details.'...'.Html::a(Yii::t('frontend','View your ticket'),$link_url)
          ];
        break;
        case 'close':
          $content=[
            'subject' => Yii::t('frontend','Ticket Closed: {subject}',['subject'=>$subject]),
            'heading' => Yii::t('frontend','Your Ticket Has Been Closed'),
            'p1' => $subject,
            'p2' => Yii::t('frontend','We will notify you when we have responded.'),
            'plain_text' => $subject.', '.$details.'...'.Html::a(Yii::t('frontend','View your ticket'),$link_url)
          ];
        break;
      }
      $button= [
        'text' => Yii::t('frontend','View the Ticket'),
        'command' => Meeting::COMMAND_VIEW_TICKET,
        'obj_id' => $ticket_id,
      ];
      // Build the absolute links to the meeting and commands
      if (isset($button)) {
        $links['button_url']=$links['view'];
        $content['button_text']=$button['text'];
      }
      // send the message
      $message = Yii::$app->mailer->compose([
        'html' => 'generic-html',
        'text' => 'generic-text'
      ],
      [
        'meeting_id' => 0,
        'sender_id'=> 0,
        'user_id' => $recipient_id,
        'auth_key' => $auth_key,
        'content'=>$content,
        'links'=>$links,
        'button'=>$button,
    ]);
    $message->setFrom(['support@meetingplanner.io'=>'Meeting Planner Support']);
    $message->setReplyTo('support@meetingplanner.io');
    $message->setTo($email)
        ->setSubject($content['subject'])
        ->send();
        \Yii::$app->language=$priorLanguage;
    }


    public static function notifyAdmin($ticket_id) {
      $u=User::find()
        ->where(['role'=>User::ROLE_ADMIN])
        ->one();
      if (isset($u)) {
        $auth_key = $u->auth_key;
        $recipient_id = $u->id;
        $links=[
          'home'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$recipient_id,$u->auth_key,0),
          'view'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_VIEW_TICKET,$ticket_id,$recipient_id,$u->auth_key,0),
          'footer_email'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_EMAIL,0,$recipient_id,$u->auth_key,0),
          'footer_block'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK,$u->id,$recipient_id,$u->auth_key,0),
          'footer_block_all'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$recipient_id,$u->auth_key,0),
        ];
      }
      $subject =Yii::t('frontend','New Support Request');
      $details = Yii::t('frontend','Please review the ticket.');
      $link_url = $links['view'];
      $content=[
        'subject' => $subject,
        'heading' => Yii::t('frontend','New Support Request'),
        'p1' => $subject,
        'p2' => $details,
        'plain_text' => $subject.', '.$details.'...'.Html::a(Yii::t('frontend','View the ticket'),$link_url)
      ];
      $button= [
        'text' => Yii::t('frontend','View the Ticket'),
        'command' => Meeting::COMMAND_VIEW_TICKET,
        'obj_id' => $ticket_id,
      ];
      $links['button_url']=$links['view'];
      $content['button_text']=$button['text'];
      // send the message
      $message = Yii::$app->mailer->compose([
        'html' => 'generic-html',
        'text' => 'generic-text'
      ],
      [
        'meeting_id' => 0,
        'sender_id'=> 0,
        'user_id' => $recipient_id,
        'auth_key' => $auth_key,
        'content'=>$content,
        'links'=>$links,
        'button'=>$button,
    ]);
    $message->setFrom(array('support@meetingplanner.io'=>'Meeting Planner Support'));
    $message->setReplyTo('support@meetingplanner.io');
    $message->setTo($u->email)
        ->setSubject($content['subject'])
        ->send();
    }
}
