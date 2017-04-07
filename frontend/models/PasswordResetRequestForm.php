<?php
namespace frontend\models;

use yii\base\Model;
use common\models\User;
use frontend\models\UserSetting;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                //'filter' => ['status' => User::STATUS_ACTIVE],
                // to do - look at this again
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            //'status' => User::STATUS_ACTIVE,
            // to do - look at again
            'email' => $this->email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            if ($user->save()) {
              $language = UserSetting::getLanguage($user->id);
              if ($language!==false) {
                \Yii::$app->language=$language;
              }
                // to do - add text version of reset your password
                // \Yii::$app->mailer->htmlLayout = '/common/mail/layouts/oxygen_html';
                return \Yii::$app->mailer->compose('passwordResetToken', ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->params['site']['title'] . ' Assistant'])
                    ->setTo($this->email)
                    ->setSubject('Password reset for ' . \Yii::$app->params['site']['title'])
                    ->send();
            }
        }
        return false;
    }
}
