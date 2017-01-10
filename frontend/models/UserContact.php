<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use filipajdacic\yiitwilio\YiiTwilio;
use common\models\Sms;

/**
 * This is the model class for table "user_contact".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $contact_type
 * @property string $info
 * @property string $details
 * @property integer $verify_code
 * @property integer $status
 * @property integer $accept_sms
 * @property integer $request_count
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $requested_at
 *
 * @property User $user
 */
class UserContact extends \yii\db\ActiveRecord
{
    public $friendly_type;
    public $verify;

    const TYPE_OTHER = 0;
    const TYPE_PHONE = 10;
    const TYPE_SKYPE = 20;
    const TYPE_FACEBOOK = 30;
    const TYPE_GOOGLE = 40;
    const TYPE_MSN = 50;
    const TYPE_AIM = 60;
    const TYPE_YAHOO = 70;
    const TYPE_ICQ = 80;
    const TYPE_JABBER = 90;
    const TYPE_QQ = 100;
    const TYPE_GADU = 110;

	const STATUS_ACTIVE = 0;
  const STATUS_VERIFIED = 5;
	const STATUS_INACTIVE = 10;

  const SETTING_NO = 0;
  const SETTING_YES = 10;

  const MAX_LIMIT = 7;

  const MAX_REQUEST_COUNT = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_contact';
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
            [['user_id', 'info' ], 'required'],
            [['user_id', 'contact_type', 'status', 'accept_sms','created_at', 'updated_at'], 'integer'],
            [['details'], 'string'],
            [['info'], 'string', 'max' => 255]
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
            'contact_type' => Yii::t('frontend', 'Contact Type'),
            'info' => Yii::t('frontend', 'Info'),
            'details' => Yii::t('frontend', 'Details'),
            'status' => Yii::t('frontend', 'Status'),
            'accept_sms' => Yii::t('frontend', 'Receive texts here?'),
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

 	public function getUserContactType($data) {
      $options = $this->getUserContactTypeOptions();
      return $options[$data];
    }

    public static function getUserContactTypeOptions()
    {
      return [
          self::TYPE_PHONE => 'Phone',
          self::TYPE_SKYPE => 'Skype',
          self::TYPE_OTHER => 'Other',
          self::TYPE_FACEBOOK => 'Facebook Messenger',
          self::TYPE_GOOGLE => 'Google Talk',
          self::TYPE_MSN => 'MSN Messenger',
          self::TYPE_AIM => 'AIM',
          self::TYPE_YAHOO => 'Yahoo! Messenger',
          self::TYPE_ICQ => 'ICQ',
          self::TYPE_JABBER => 'Jabber',
          self::TYPE_QQ => 'QQ',
          self::TYPE_GADU => 'Gadu-Gadu',
      ];
     }

  public static function get($user_id) {
    $contacts = UserContact::find()->where(['user_id'=>$user_id])->all();
    return $contacts;
  }

  public static function countContacts($user_id) {
    $cnt = UserContact::find()->where(['user_id'=>$user_id])->count();
    return $cnt;
  }

  public static function getUserContactList($user_id) {
    $optionList = UserContact::getUserContactTypeOptions();
    $contacts = UserContact::find()->where(['user_id'=>$user_id,'status'=>UserContact::STATUS_ACTIVE])->all();
    foreach ($contacts as $c) {
      // add type string
      $c->friendly_type=$optionList[$c['contact_type']];
    }
    return $contacts;
  }

  public static function buildContactString($user_id,$mode='ical') {
    // to do - create a view for this that can be rendered
    $contacts = UserContact::getUserContactList($user_id);
    if (count($contacts)==0) return '';
    if ($mode=='ical') {
        $str='';
    } else if ($mode =='html') {
        $str='<p>';
    }
    $str = \common\components\MiscHelpers::getDisplayName($user_id).': ';
    if ($mode=='ical') {
        $str.=' \\n';
    } else if ($mode =='html') {
        $str.='<br />';
    }
    foreach ($contacts as $c) {
      if ($mode=='ical') {
        $str.=$c->friendly_type.': '.$c->info.' ('.$c->details.')\\n';
      } else if ($mode =='html') {
        $str.=$c->friendly_type.': '.$c->info.'<br />'.$c->details.'<br />';
      }
    }
    if ($mode=='ical') {
        $str.=' \\n';
    } else if ($mode =='html') {
        $str.='</p>';
    }
    return $str;
  }

  public function clearOtherNumbers() {
    // to do - turn off sms for other numbers
    //https://app.asana.com/0/138933783917168/157333263929755
  }

  public static function withinLimit($user_id) {
    // check max
    $cnt = UserContact::find()
      ->where(['user_id'=>$user_id])
      ->count();
    if ($cnt >= UserContact::MAX_LIMIT ) {
      return false;
    }
    return true;
  }

  public function canRequest() {
    if ($this->request_count<UserContact::MAX_REQUEST_COUNT) {
      if (time() - $this->requested_at>=60) {
        return true;
      } else {
          return Yii::t('frontend','Sorry, you must wait a minute between requests.');
      }
    } else {
      return Yii::t('frontend','You have exceeded the maximum number of attempts.');
    }
  }

  public function requestCode() {
    $this->verify_code = rand(0,9999);
    $this->requested_at = time();
    $this->request_count+=1;
    $this->update();
    $sms = new Sms;
    $sms->transmit($this->info,Yii::t('frontend','Please return to the site and type in {code}',['code'=>sprintf("%04d",$this->verify_code)]));
  }

  public function findUserNumber($user_id,$status) {
    $uc = UserContact::find()
      ->where(['user_id'=>$user_id])
      ->andWhere(['contact_type'=>UserContact::TYPE_PHONE])
      ->andWhere(['accept_sms'=>1])
      ->andWhere(['status'=>$status])
      ->one();
    if (is_null($uc) || count($uc)==0) {
      return false;
    } else {
      return $uc->info;
    }
  }

}
