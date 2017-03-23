<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%domain}}".
 *
 * @property integer $id
 * @property string $domain
 * @property integer $level
 * @property integer $created_at
 * @property integer $updated_at
 */
class Domain extends \yii\db\ActiveRecord
{
  const LEVEL_BLACK = 0;
  const LEVEL_WHITE = 10;

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
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'domain' => Yii::t('frontend', 'Domain'),
            'level' => Yii::t('frontend', 'Level'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public static function verify($domain) {
      $v = Domain::find()
        ->where(['domain'=>$domain])
        ->andWhere(['level'=>Domain::LEVEL_BLACK])
        ->one();
      if (is_null($v)) {
        // not a blacklisted domain
        return true;
      } else {
        // a blacklisted domain
        return false;
      }
    }
}
