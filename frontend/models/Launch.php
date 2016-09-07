<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "launch".
 *
 * @property integer $id
 * @property string $email
 * @property string $ip_addr
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Launch extends \yii\db\ActiveRecord
{
  const STATUS_REQUEST =0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'launch';
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
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],            
            [['email', 'ip_addr'], 'string', 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email', 'checkDNS'=>true, 'enableIDN'=>true],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'email' => Yii::t('frontend', 'Email'),
            'ip_addr' => Yii::t('frontend', 'Ip Addr'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public static function add($email) {
      $l = new Launch;
      $l->email = $email;
      $l->status=Launch::STATUS_REQUEST;
      $l->ip_addr = Yii::$app->getRequest()->getUserIP();
      $l->save();
      return true;
    }
}
