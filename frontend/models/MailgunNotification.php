<?php

namespace frontend\models;

use Yii;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use common\models\Yiigun;
use common\components\MiscHelpers;

/**
 * This is the model class for table "mailgun_notification".
 *
 * @property integer $id
 * @property string $url
 * @property integer $created_at
 * @property integer $updated_at
 */
class MailgunNotification extends \yii\db\ActiveRecord
{
  const STATUS_PENDING = 0;
  const STATUS_READ = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mailgun_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url',], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['url'], 'string', 'max' => 255],
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'url' => Yii::t('frontend', 'Url'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    public function process() {
      $yg = new Yiigun();
      //$yg->send_simple_message('jeff@meetingplanner.io','newscloud@gmail.com','Monitor Test Result','because i said so');
      $response = $yg->get('domains/meetingplanner.io/messages/eyJwIjogZmFsc2UsICJrIjogIjdiYWMyZDNjLTRkNGYtNDIzMy04NDU1LTM3ZmMyMjc1YWRmMiIsICJzIjogIjIwYzNkNWRhY2YiLCAiYyI6ICJ0YW5rczIifQ==');
      var_dump($response);
    }
}
