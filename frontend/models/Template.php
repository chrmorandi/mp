<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use yii\i18n\Formatter;
use common\models\User;
use common\components\MiscHelpers;

/**
 * This is the model class for table "template".
 *
 * @property integer $id
 * @property integer $owner_id
 * @property string $name
 * @property integer $meeting_type
 * @property string $message
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $subject
 *
 * @property User $owner
 * @property TemplatePlace[] $templatePlaces
 * @property TemplateTime[] $templateTimes
 */
class Template extends \yii\db\ActiveRecord
{
  const STATUS_ACTIVE =0;
  const STATUS_DELETED = 70;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'template';
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
            [['owner_id', 'meeting_type', 'status', 'name', 'subject'], 'required'],
            [['owner_id', 'meeting_type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['message'], 'string'],
            [['name', 'subject'], 'string', 'max' => 255],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'owner_id' => Yii::t('frontend', 'Owner ID'),
            'name' => Yii::t('frontend', 'Template Name'),
            'meeting_type' => Yii::t('frontend', 'Type of Meeting'),
            'message' => Yii::t('frontend', 'Message'),
            'status' => Yii::t('frontend', 'Status'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
            'subject' => Yii::t('frontend', 'Subject'),
        ];
    }

    public static function countUserTemplates($user_id) {
      // number of meetings owned or participated in
      return Template::find()->where(['owner_id'=>$user_id])->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplatePlaces()
    {
        return $this->hasMany(TemplatePlace::className(), ['template_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplateTimes()
    {
        return $this->hasMany(TemplateTime::className(), ['template_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return TemplateQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TemplateQuery(get_called_class());
    }

    public function buildMeeting($template_id) {
      // create a meeting from the template

    }
}
