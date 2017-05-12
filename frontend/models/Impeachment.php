<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
/**
 * This is the model class for table "{{%impeachment}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $referral_id
 * @property integer $estimate
 * @property integer $month
 * @property integer $daystamp
 * @property integer $year
 * @property string $monthyear
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Impeachment extends \yii\db\ActiveRecord
{
  public $hour;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%impeachment}}';
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
            [['user_id', 'estimate', 'month', 'daystamp','year', 'monthyear',  ], 'required'],
            [['user_id', 'referral_id',  'month', 'daystamp', 'year',], 'integer'],
            [['monthyear'], 'string', 'max' => 10],
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
            'referral_id' => Yii::t('frontend', 'Referral ID'),
            'estimate' => Yii::t('frontend', 'Estimate'),
            'month' => Yii::t('frontend', 'Month'),
            'daystamp' => Yii::t('frontend', 'Daystamp'),
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
     * @return ImpeachmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ImpeachmentQuery(get_called_class());
    }

  public static function hoursArray() {
    $hour =5;
    while ($hour <24) {
      $hourList[$hour]=($hour<=12?$hour:$hour-12).':00 '.($hour<12?Yii::t('frontend','am'):Yii::t('frontend','pm'));
      $hour+=1;
    }
    $hourList[24]='midnight';
    //var_dump($hourList);exit;
    return $hourList;
  }
}
