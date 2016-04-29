<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_block".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $blocked_user_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserBlock extends \yii\db\ActiveRecord
{
    const MODE_BLOCK = 0;
    const MODE_CLEAR = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_block';
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
            [['user_id', 'blocked_user_id'], 'required'],
            [['user_id', 'blocked_user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'blocked_user_id' => Yii::t('frontend', 'Blocked User ID'),
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
     * @return UserBlockQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserBlockQuery(get_called_class());
    }

    public static function add($user_id,$blocked_user_id,$mode = UserBlock::MODE_BLOCK) {
      $ub = new UserBlock;
      $blks = UserBlock::find()->where(['user_id'=>$user_id,'blocked_user_id'=>$blocked_user_id])->one();
      if (is_null($blks)) {
        // add the block
        $ub->user_id = $user_id;
        $ub->blocked_user_id = $blocked_user_id;
        $ub->save();
      } else {
        if ($mode == UserBlock::MODE_CLEAR) {
          // remove the block
          $blks->delete();
        }
      }
    }
}
