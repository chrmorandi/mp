<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;
use backend\models\MessageLog;

/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $subject
 * @property string $caption
 * @property string $content
 * @property string $action_text
 * @property string $action_url
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Message extends \yii\db\ActiveRecord
{

  const STATUS_DRAFT = 0;
  const STATUS_TEST = 10;
  const STATUS_SENT = 20;
  const STATUS_IN_PROGRESS = 25;
  const STATUS_ALL_SENT = 30;
  const STATUS_TRASH = 50;

  const RESPONSE_NO = 0;
  const RESPONSE_YES = 10;
  const RESPONSE_NO_UPDATES = 20;
  const RESPONSE_INVALID_EMAIL = 60;

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
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['caption', 'content'], 'required'],
            [['caption', 'content'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['subject', 'action_text', 'action_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'subject' => Yii::t('backend', 'Subject'),
            'caption' => Yii::t('backend', 'Caption'),
            'content' => Yii::t('backend', 'Content'),
            'action_text' => Yii::t('backend', 'Action Text'),
            'action_url' => Yii::t('backend', 'Action Url'),
            'status' => Yii::t('backend', 'Status'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }

    public function displayStatus() {
      switch ($this->status) {
        case Message::STATUS_IN_PROGRESS:
          return 'In Progress';
          break;
        case Message::STATUS_ALL_SENT:
          return 'All Sent';
          break;
        case Message::STATUS_SENT:
          return 'Sent';
          break;
        case Message::STATUS_TRASH:
          return 'Deleted';
          break;
        default:
          return 'Draft';
          break;
      }
    }

    public function trash($user_id) {
      // to do - check if user is an admin
      if (User::findOne($user_id)->isAdmin() && $this->status == self::STATUS_DRAFT) {
        $this->status = self::STATUS_TRASH;
        $this->update();
        return true;
      } else {
        return false;
      }
    }

    public function test($id) {
      $u = User::findOne(Yii::$app->user->getId());
      if ($u->isAdmin()) {
        // send to this administrator
        $msg = Message::findOne($id);
        $this->sendOne($msg,$u);
      } else {
        echo 'not admin';exit;
      }

    }

    public function findNextGroup($limit = 10) {
      // find the next group of users we haven't sent this message to
      // to do - problem with left join with deleted users and limits
      $users = User::find()
      //->select('user.id,user.email')
        ->leftJoin('message_log','message_log.user_id=user.id')
        ->where('message_log.id is null')
        ->andWhere('status!='.User::STATUS_DELETED)
        ->limit($limit)
        ->all();
    return $users;
    }

    public function send($id,$limit = 10) {
      if (User::findOne(Yii::$app->user->getId())->isAdmin()) {
      //$user = Yii::$app->getUser();
      //if (!$user->isAdmin()) {
        $msg = Message::findOne($id);
        $users = $this->findNextGroup($limit);
        if (is_null($users)) {
          $msg->status=Message::STATUS_ALL_SENT;
        } else {
          echo 'Preparing to send...<br />';
          $msg->status=Message::STATUS_IN_PROGRESS;
          foreach ($users as $u) {
            try {
              echo 'To: '.$u->email.'<br />';
      		    $this->sendOne($msg,$u);
      	    } catch (Exception $e) {
      		      echo 'Exception '.$e.'<br />';
      	    }
          }
        }
        $msg->update();
        echo 'Completed<br />';
        exit;
      } else {
        echo 'not admin';exit;
      }

    }

    public function sendOne($msg,$u) {
      // ensure there is an auth key for the recipient user
      $user_id = $u->id;
      if (empty($u->auth_key) || empty($u->email)) {
        echo 'No auth key or empty email<br />';
        return false;
      }
      // prepare data for the message
      $a=['user_id'=>$user_id,
       'auth_key'=>$u->auth_key,
       'email'=>$u->email,
       'username'=>$u->username
      ];
       // check if email is okay and okay from this sender_id
       // to do - extend checkEmailDelivery
      if (User::checkEmailDelivery($user_id,0)) {
        echo '-->email delivery ok<br />';
          // CAUTION - beware backend message sending generates links to backend site not frontend
          // Build the absolute links to the meeting and commands
          $links=[
            'home'=>MiscHelpers::backendBuildCommand(0,Meeting::COMMAND_HOME,$msg->id,$a['user_id'],$a['auth_key']),
            'footer_email'=>MiscHelpers::backendBuildCommand(0,Meeting::COMMAND_FOOTER_EMAIL,$msg->id,$a['user_id'],$a['auth_key']),
            'footer_block_updates'=>MiscHelpers::backendBuildCommand(0,Meeting::COMMAND_NO_UPDATES,$msg->id,$a['user_id'],$a['auth_key']),
            'footer_block_all'=>MiscHelpers::backendBuildCommand(0,Meeting::COMMAND_FOOTER_BLOCK_ALL,$msg->id,$a['user_id'],$a['auth_key']),
            'action_url'=>MiscHelpers::backendBuildCommand(0,Meeting::COMMAND_RESPOND_MESSAGE,$msg->id,$a['user_id'],$a['auth_key']),
          ];
          // send the message
          $message = Yii::$app->mailer->compose([
            'html' => 'project-update-html',
            'text' => 'project-update-text'
          ],
          [
            'user_id' => $a['user_id'],
            'auth_key' => $a['auth_key'],
            'mode' => 'update', // used in footer
            'links' => $links,
            'msg'=>$msg,
        ]);
          if (!empty($a['email'])) {
            // recheck validity of outbound email
            if (filter_var($a['email'], FILTER_VALIDATE_EMAIL)) {
              echo '-->all okay,send and log it <br />';
              $message->setFrom(['support@meetingplanner.io'=>'Meeting Planner'])
                ->setTo($a['email'])
                ->setSubject(Yii::t('backend','').$msg->subject)
                ->send();
                MessageLog::add($msg->id,$user_id);
            } else {
              echo '-->invalid email, log it <br />';
              MessageLog::add($msg->id,$user_id,Message::RESPONSE_INVALID_EMAIL);
            }

          }
       } else {
         echo '--> No email delivery<br />';
       }
    }

    public static function respond($message_id,$user_id,$response) {
        // user clicked on action_url command
        $msg = Message::findOne($message_id);
        // record the response
        MessageLog::recordResponse($message_id,$user_id,$response);
        // process redirect
        if ($msg->action_url=='') {
          return Url::home(true);
        } else {
          return $msg->action_url;
        }
    }
}
