<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "{{%ticket_reply}}".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property integer $posted_by
 * @property string $reply
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Ticket $ticket
 */
class TicketReply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ticket_reply}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'posted_by', 'reply', 'created_at', 'updated_at'], 'required'],
            [['ticket_id', 'posted_by', 'created_at', 'updated_at'], 'integer'],
            [['reply'], 'string'],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'ticket_id' => Yii::t('frontend', 'Ticket ID'),
            'posted_by' => Yii::t('frontend', 'Posted By'),
            'reply' => Yii::t('frontend', 'Reply'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    /**
     * @inheritdoc
     * @return TicketReplyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketReplyQuery(get_called_class());
    }
}
