<?php

namespace backend\models;

use Yii;
use backend\models\Message;

/**
 * This is the model class for table "message_log".
 *
 * @property integer $id
 * @property integer $message_id
 * @property integer $user_id
 *
 * @property User $user
 * @property Message $message
 */
class MessageLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'user_id', 'response'], 'required'],
            [['message_id', 'user_id', 'response'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['message_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'message_id' => Yii::t('backend', 'Message ID'),
            'user_id' => Yii::t('backend', 'User ID'),
             'response' => Yii::t('backend', 'Response'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }

    /**
     * @inheritdoc
     * @return MessageLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MessageLogQuery(get_called_class());
    }

    public static function add($message_id,$user_id,$response = Message::RESPONSE_NO) {
				 $log = new MessageLog;
         // don't add duplicates
         if (MessageLog::find()->where(['message_id'=>$message_id,'user_id'=>$user_id])->count()>0) {
           return false;
         }
         $log->message_id=$message_id;
         $log->user_id =$user_id;
         $log->response = $response;
         $log->save();
    }

    public static function recordResponse($message_id,$user_id,$response) {
      $ml = MessageLog::find()->where(['message_id'=>$message_id,'user_id'=>$user_id])->one();
      if (is_null($ml)) {
        return false;
      }
      $ml->response = $response; // yes or no_updates
      $ml->update();
    }

    public function displayResponse() {
      switch ($this->response) {
        case Message::RESPONSE_NO:
          return 'None';
          break;
        case Message::RESPONSE_YES:
          return 'Opened';
          break;
        case Message::RESPONSE_NO_UPDATES:
          return 'No updates';
          break;
        case Message::RESPONSE_INVALID_EMAIL:
          return 'Invalid email';
          break;
        case Message::RESPONSE_DELIVERY_OFF:
          return 'Delivery off';
          break;
      default:
          return 'n/a';
          break;
      }
    }


}
