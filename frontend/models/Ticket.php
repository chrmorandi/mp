<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property integer $id
 * @property integer $posted_by
 * @property string $question
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property TicketReply[] $ticketReplies
 */
class Ticket extends \yii\db\ActiveRecord
{
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
            [['posted_by', 'question'], 'required'],
            [['posted_by', 'status', 'created_at', 'updated_at'], 'integer'],
            [['question'], 'string'],
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
            'question' => Yii::t('frontend', 'Question'),
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
}
