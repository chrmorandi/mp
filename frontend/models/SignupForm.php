<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\validators\EmailValidator;
use common\models\User;
use frontend\models\Domain;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('frontend','This username has already been taken.')],
            ['username', 'string', 'min' => 5, 'max' => 32],
            ['username', 'notNumeric'],
            ['username', 'notNumericFirst'],
            ['username','noSpaces'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email','safeEmail'],
            ['email', 'email', 'checkDNS'=>true, 'enableIDN'=>true],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('frontend','This email address has already been taken. ').Html::a(Yii::t('frontend','Looking for your password?'), ['site/request-password-reset'])],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            //['captcha', 'required'],
            //['captcha', 'captcha'],
        ];
    }

    public function safeEmail($attribute, $params)
    {
      $tempEmail = explode('@',$this->$attribute);
      $emailDomain = end($tempEmail);
      // check domain against blacklist
      if (!Domain::verify($emailDomain)) {
        $this->addError($attribute, Yii::t('frontend','Sorry, we do not support your email address.'));
      }
    }

    public function noSpaces($attribute, $params)
    {
      if (stristr($this->$attribute,' ')!==false) {
        $this->addError($attribute, Yii::t('frontend','Sorry, we do not allow spaces in your username.'));
      }
    }

    public function notNumeric($attribute, $params)
    {
      if (is_numeric($this->$attribute)) {
        $this->addError($attribute, Yii::t('frontend','Sorry, we do not allow numeric usernames. Try adding a letter.'));
      }
    }

    public function notNumericFirst($attribute, $params)
    {
      if (is_numeric($this->$attribute[0])) {
        $this->addError($attribute, Yii::t('frontend','Sorry, usernames cannot begin with a number'));
      }
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save();
            $user->completeInitialize($user->id);
            return $user;
        }
        return null;
    }
}
