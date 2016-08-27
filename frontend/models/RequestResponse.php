<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "request_response".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $responder_id
 * @property string $note
 * @property integer $response
 * @property integer $created_at
 * @property integer $updated_at
 */
class RequestResponse extends \yii\db\ActiveRecord
{
  const RESPONSE_NONE = 0;
  const RESPONSE_ACCEPT = 10;
  const RESPONSE_REJECT = 20;
  const RESPONSE_LIKE = 30;
  const RESPONSE_DISLIKE = 40;
  const RESPONSE_NEUTRAL = 50;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_response';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'responder_id', 'response'], 'integer'],
            [['note'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'request_id' => Yii::t('frontend', 'Request ID'),
            'responder_id' => Yii::t('frontend', 'Responder ID'),
            'note' => Yii::t('frontend', 'Note'),
            'response' => Yii::t('frontend', 'Response'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
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

    public function lookupOpinion() {
      switch ($this->response) {
        case RequestResponse::RESPONSE_LIKE:
          $str = 'likes';
        break;
        case RequestResponse::RESPONSE_DISLIKE:
          $str = 'dislikes';
        break;
        case RequestResponse::RESPONSE_NEUTRAL:
          $str = 'is neutral';
        break;
      }
      return $str;
    }
}
