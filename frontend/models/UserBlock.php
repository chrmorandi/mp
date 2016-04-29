<?php

namespace frontend\models;

use Yii;

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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_block';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'blocked_user_id', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'blocked_user_id', 'created_at', 'updated_at'], 'integer'],
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
}
