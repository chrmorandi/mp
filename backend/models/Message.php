<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\components\MiscHelpers;
use frontend\models\Meeting;

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
  const STATUS_TRASH = 50;

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
        case Message::STATUS_SENT:
          return 'sent';
          break;
        case Message::STATUS_TRASH:
          return 'deleted';
          break;
        default:
          return 'draft';
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
      if (User::findOne(Yii::$app->user->getId())->isAdmin()) {
        // send to this administrator
        $msg = Message::findOne($id);
        $msg->sendOne($id,Yii::$app->user->getId());
      } else {
        echo 'not admin';exit;
      }

    }

    public function send($id) {
      if (User::findOne(Yii::$app->user->getId())->isAdmin()) {

      } else {
        echo 'not admin';exit;
      }

    }

    public function sendone($id,$user_id) {
      $msg = Message::findOne($id);
      $u = \common\models\User::findOne($user_id);
      // ensure there is an auth key for the recipient user
      if (empty($u->auth_key)) {
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
          // Build the absolute links to the meeting and commands
          $links=[
            'home'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_HOME,0,$a['user_id'],$a['auth_key']),
            'footer_email'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_EMAIL,0,$a['user_id'],$a['auth_key']),
            'footer_block_all'=>MiscHelpers::buildCommand(0,Meeting::COMMAND_FOOTER_BLOCK_ALL,0,$a['user_id'],$a['auth_key']),
          ];
          // send the message
          $message = Yii::$app->mailer->compose([
            'html' => 'project-update-html',
            'text' => 'project-update-text'
          ],
          [
            'user_id' => $a['user_id'],
            'auth_key' => $a['auth_key'],
            'mode' => 'update2',
            'links' => $links,
        ]);
          if (!empty($a['email'])) {
            $message->setFrom(['support@meetingplanner.com'=>'Meeting Planner']);
            $message->setTo($a['email'])
                ->setSubject(Yii::t('frontend','Meeting Planner Update: ').$msg->subject)
                ->send();
          }
       }
      $msg->status=Message::STATUS_TEST;
      $msg->update();
    }
}
