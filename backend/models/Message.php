<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "message".
 *
 * @property integer $id
 * @property string $subject
 * @property string $caption
 * @property string $content
 * @property string $action_text
 * @property string $action_url
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Message extends \yii\db\ActiveRecord
{

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
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['caption', 'content'], 'required'],
            [['caption', 'content'], 'string'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['subject', 'action_text', 'action_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'subject' => Yii::t('backend', 'Subject'),
            'caption' => Yii::t('backend', 'Caption'),
            'content' => Yii::t('backend', 'Content'),
            'action_text' => Yii::t('backend', 'Action Text'),
            'action_url' => Yii::t('backend', 'Action Url'),
            'status' => Yii::t('backend', 'Status'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
        ];
    }
}
