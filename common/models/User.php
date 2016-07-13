<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\UserBlock;
use frontend\models\UserSetting;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{

    // note: validation rule uses range of status variables
    const STATUS_DELETED = 0;
    const STATUS_UNVERIFIED = 5;
    const STATUS_ACTIVE = 10;
    const STATUS_PASSIVE = 20;

    const ROLE_USER = 10;
    const ROLE_ADMIN = 100;

    public $dataCount;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [ self::STATUS_DELETED,self::STATUS_ACTIVE,self::STATUS_PASSIVE,self::STATUS_UNVERIFIED]],

            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER,self::ROLE_ADMIN]],

        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
        // , 'status' => self::STATUS_ACTIVE
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne([
            'password_reset_token' => $token,
            'status' => [self::STATUS_ACTIVE,self::STATUS_PASSIVE,self::STATUS_UNVERIFIED],
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function fetchPasswordResetToken() {
        if (is_null($this->password_reset_token)) {
          $this->generatePasswordResetToken();
          $this->save();
        }
        return $this->password_reset_token;
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isAdmin() {
      if ($this->role == User::ROLE_ADMIN) {
        return true;
      } else {
        return false;
      }
    }

    public static function checkEmailDelivery($user_id,$sender_id=0) {
      // check if this user_id receives email and if sender_id not blocked
      // check that account isn't Deleted
      $u = User::findOne($user_id);
      if ($u->status == User::STATUS_DELETED) {
        return false;
      }
      // check if all email is turned off
      $us = UserSetting::safeGet($user_id);
      if ($us->no_email != UserSetting::EMAIL_OK) {
        return false;
      }
      // check if no sender i.e. system notification
      if ($sender_id==0) {
        return true;
      }
      // check if sender is blocked
      $ub = UserBlock::find()->where(['user_id'=>$user_id,'blocked_user_id'=>$sender_id])->one();
      if (!is_null($ub)) {
        return false;
      }
      return true;
    }

    public static function completeInitialize($user_id) {
      \frontend\models\UserProfile::initialize($user_id);
      \frontend\models\UserSetting::initialize($user_id);
      \frontend\models\Reminder::initialize($user_id);
    }

    public static function findByEmail($email) {
      $u = static::findOne(['email'=>$email]);
      if (is_null($u)) {
        // user doesn't exist
        return false;
      } else {
        return $u;
      }
    }

    public static function lookupStatus($status) {
      switch ($status) {
        case User::STATUS_ACTIVE:
          $label = Yii::t('frontend','Active');
        break;
        case User::STATUS_UNVERIFIED:
          $label = Yii::t('frontend','Email unverified');
        break;
        case User::STATUS_PASSIVE:
          $label = Yii::t('frontend','Via invite');
        break;
        case User::STATUS_DELETED:
          $label = Yii::t('frontend','Deleted');
        break;
      }
      return $label;
    }

    public static function sendVerifyEmail($user_id,$meeting_id) {
      // to do - add text version of verify email
      // \Yii::$app->mailer->htmlLayout = '/common/mail/layouts/oxygen_html';
      $u = User::findOne($user_id);
      $verifyLink = \common\components\MiscHelpers::buildCommand($meeting_id,\frontend\models\Meeting::COMMAND_VERIFY_EMAIL,0,$user_id,$u->auth_key);
      \frontend\models\MeetingLog::add($meeting_id,\frontend\models\MeetingLog::ACTION_SENT_EMAIL_VERIFICATION,$user_id,0);
      return \Yii::$app->mailer->compose('verification-html', ['user' => $u,'verifyLink'=>$verifyLink])
          ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' Assistant'])
          ->setTo($u->email)
          ->setSubject('Verify Your Email Address for ' . \Yii::$app->name)
          ->send();
    }


}
