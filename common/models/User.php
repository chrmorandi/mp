<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\UserBlock;
use frontend\models\UserSetting;
use yii\helpers\Inflector;

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

    const ROLE_USER = 10; // to do - change from STATUS_ACTIVE
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

    public static function findByEmail($email) {
      $u = static::findOne(['email'=>$email]);
      if (is_null($u)) {
        // user doesn't exist
        return false;
      } else {
        return $u;
      }
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
      return (int)$this->role === User::ROLE_ADMIN;
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
      // to do - there is a bug in userprofile validation that requires login status
      // \frontend\models\UserProfile::initialize($user_id);
      \frontend\models\UserSetting::initialize($user_id);
      \frontend\models\Reminder::initialize($user_id);
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
      if (User::checkEmailDelivery($user_id)) {
          $u = User::findOne($user_id);
          $site_id = \frontend\models\Meeting::getSiteFromMeeting($meeting_id);
          $verifyLink = \common\components\MiscHelpers::buildCommand($meeting_id,\frontend\models\Meeting::COMMAND_VERIFY_EMAIL,0,$user_id,$u->auth_key,$site_id);
          \frontend\models\MeetingLog::add($meeting_id,\frontend\models\MeetingLog::ACTION_SENT_EMAIL_VERIFICATION,$user_id,0);
          $language = UserSetting::getLanguage($user_id);
          if ($language!==false) {
            \Yii::$app->language=$language;
          }
          return \Yii::$app->mailer->compose(['html'=>'verification-html','text' => 'verification-text'], ['user' => $u,'verifyLink'=>$verifyLink])
              ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['site']['title'] . ' Assistant'])
              ->setTo($u->email)
              ->setSubject('Verify Your Email Address for ' . \Yii::$app->params['site']['title'])
              ->send();
      }
    }

    public static function generateUniqueUsername($prefix='',$mode = 'form') {
      $is_unique = false;
      $cnt = 0;
      $username=$prefix;
      while (!$is_unique && $cnt<15) {
        $u = User::find()->where(['username'=>$username])->one();
        if (is_null($u)) {
          return $username;
        } else {
          $cnt+=1;
        }
        $username.=Yii::$app->security->generateRandomString(1);
      }
      if ($mode =='form') {
        echo 'Sorry, we were unable to generate a unique username for you.';
        exit();
      } else {
        // called from addUserFromEmail
        return false;
      }

    }

    public static function addUserFromEmail($email) {
      // safely creates and initializes new user account
      // called from Participant added and Friend added
      // but if already exists, returns user_id
      if (User::find()->where(['email' => $email])->exists()) {
        return User::find()->where(['email' => $email])->one()->id;
      }
      $user = new User();
      $user->email = $email;
      $unique_username = User::generateUniqueUsername($email);
      if ($unique_username===false) {
        // to do - log or email major error
        exit;
      }
      $user->username = $unique_username;
      $user->status = User::STATUS_PASSIVE;
      $pwd = Yii::$app->security->generateRandomString(12);
      $user->setPassword($pwd);
      $user->generateAuthKey();
      $user->save();
      // to do - report error
      $user->completeInitialize($user->id);
      return $user->id;
    }

    public function displayConstant($lookup) {
       $xClass = new \ReflectionClass ( get_class($this));
     	$constants = $xClass->getConstants();
     	$constName = null;
     	foreach ( $constants as $name => $value )
     	{
     		if ($value == $lookup)
     		{
     			return strtolower($name);
     		}
     	}
     }

    public static function checkAllUsers() {
      $fullReport = new \stdClass;
      $fullReport->result = true;
        $users = User::find()
          ->where(['status'=>User::STATUS_ACTIVE])
          ->orWhere(['status'=>User::STATUS_PASSIVE])
          ->all();
        foreach ($users as $u) {
          $report = User::isInitialized($u->id);
          if ($report->result===false) {
            $fullReport->result = false;
            $fullReport->errors[]='User: '.$u->email;
            foreach ($report->errors as $e) {
              $fullReport->errors[]=$e;
            }
          }
        }
        return $fullReport;
    }

    public static function isInitialized($user_id) {
      // have a user profile entry
      // have a user setting entry
      // have reminders
      $report = new \stdClass;
      $report->result = true;
      $up = \frontend\models\UserProfile::find()->where(['user_id'=>$user_id])->one();
      if (is_null($up)) {
        $report->result = false;
        $report->errors[] = $user_id.' has no UserProfile';
      }
      $us = \frontend\models\UserSetting::find()->where(['user_id'=>$user_id])->one();
      if (is_null($us)) {
        $report->result = false;
        $report->errors[] = $user_id.' has no UserSettings';
      }
      $rems = \frontend\models\Reminder::find()->where(['user_id'=>$user_id])->count();
      if ($rems ==0) {
        $report->result = false;
        $report->errors[] = 'Warning: '.$user_id.' has no Reminders';
      }
      return $report;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
          if ($insert) {
            $this->site_id = Yii::$app->params['site']['id'];
          }
        }
        return true;
    }

    public static function deleteAccount($user_id) {
      $u = User::findOne($user_id);
      $u->status = User::STATUS_DELETED;
      $u->update();
    }
}
