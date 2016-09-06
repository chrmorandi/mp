<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $status
 * @property string $firstname
 * @property string $lastname
 * @property string $fullname
 * @property string $email
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Address extends \yii\db\ActiveRecord
{
    const STATUS_RAW = 0;
    const CONTACTS_PAGE_SIZE = 2500;

    public $address_type;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
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
            [['user_id', 'firstname', 'lastname', 'fullname', 'email'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['firstname', 'lastname', 'fullname', 'email'], 'string', 'max' => 255],
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
            'status' => Yii::t('frontend', 'Status'),
            'firstname' => Yii::t('frontend', 'Firstname'),
            'lastname' => Yii::t('frontend', 'Lastname'),
            'fullname' => Yii::t('frontend', 'Fullname'),
            'email' => Yii::t('frontend', 'Email'),
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
     * @return AddressQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AddressQuery(get_called_class());
    }

    public function add($data) {
        $dup = $this->find()
          ->where(['email'=>$data['email']])
          ->count();
        if ($dup>0) {
          return false;
        }
        $user_id = Yii::$app->user->getId();
        $a = new Address();
        $a->user_id = $user_id;
        $a->status = Address::STATUS_RAW;
        $a->email = $data['email'];
        $a->fullname = $data['fullname'];
        $a->firstname = $data['firstname'];
        $a->lastname = $data['lastname'];
        $a->save();
    }

    public function transfer() {
      // to do - use email validation with friend
      // check validation
      // change status here
    }

    public static function curl($url, $post = "") {
       $curl = curl_init();
       $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
       curl_setopt($curl, CURLOPT_URL, $url);
       //The URL to fetch. This can also be set when initializing a session with curl_init().
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
       //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
       curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
       //The number of seconds to wait while trying to connect.
       if ($post != "") {
       curl_setopt($curl, CURLOPT_POST, 5);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
       }
       curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
       //The contents of the "User-Agent: " header to be used in a HTTP request.
       curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
       //To follow any "Location: " header that the server sends as part of the HTTP header.
       curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE);
       //To automatically set the Referer: field in requests where it follows a Location: redirect.
       curl_setopt($curl, CURLOPT_TIMEOUT, 10);
       //The maximum number of seconds to allow cURL functions to execute.
       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
       //To stop cURL from verifying the peer's certificate.
       $contents = curl_exec($curl);
       curl_close($curl);
       return $contents;
     }
}
