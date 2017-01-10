<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%ticket_reply}}".
 *
 * @property integer $id
 * @property integer $ticket_id
 * @property string $posted_by
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
            [['ticket_id', 'posted_by', 'reply'], 'required'],
            [['ticket_id', 'created_at', 'updated_at'], 'integer'],
            [['reply','posted_by'], 'string'],
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
