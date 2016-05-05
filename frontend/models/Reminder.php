<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;
use common\models\User;

/**
 * This is the model class for table "reminder".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $duration_friendly
 * @property integer $unit
 * @property integer $duration
 * @property integer $reminder_type
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Reminder extends \yii\db\ActiveRecord
{
  const UNIT_MINUTES = 0;
  const UNIT_HOURS = 10;
  const UNIT_DAYS = 20;

  const TYPE_EMAIL = 0;
  const TYPE_SMS = 10;
  const TYPE_BOTH = 20;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reminder';
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
            [['user_id'], 'required'],
            [['user_id', 'duration_friendly', 'unit', 'duration', 'reminder_type', 'created_at', 'updated_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'duration_friendly' => Yii::t('frontend', 'Duration Friendly'),
            'unit' => Yii::t('frontend', 'Unit'),
            'duration' => Yii::t('frontend', 'Duration'),
            'reminder_type' => Yii::t('frontend', 'Reminder Type'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return ReminderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ReminderQuery(get_called_class());
    }

    public static function initialize($user_id) {
      // create initial reminders for a user
      $r1 = new Reminder();
      $r1->user_id = $user_id;
      $r1->duration_friendly = 1;
      $r1->unit = Reminder::UNIT_HOURS;
      $r1->reminder_type = Reminder::TYPE_EMAIL;
      $r1->duration = 3600;
      $r1->validate();
      var_dump($r1->getErrors());
      $r1->save();
      $r2 = new Reminder();
      $r2->user_id = $user_id;
      $r2->duration_friendly = 1;
      $r2->unit = Reminder::UNIT_DAYS;
      $r2->reminder_type = Reminder::TYPE_EMAIL;
      $r2->duration = 1*24*3600;
      $r2->save();
    }
}
