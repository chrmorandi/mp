<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property integer $id
 * @property integer $posted_by
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
  const STATUS_PENDING_USER = 20;
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
            [['posted_by', 'status', 'created_at', 'updated_at'], 'integer'],
            [['details','subject'], 'string'],
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

    public function getStatus() {
      switch ($this->status) {
        case Ticket::STATUS_OPEN:
          return Yii::t('frontend','Open');
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
}
