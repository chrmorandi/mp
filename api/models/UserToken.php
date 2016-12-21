<?php
/**
 * @link https://meetingplanner.io
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/newscloud/mp/blob/master/LICENSE
 */
namespace api\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use frontend\models\UserProfile;
/**
 * This is the model class for table "user_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_token';
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
            [['user_id','token' ], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('api', 'ID'),
            'user_id' => Yii::t('api', 'User ID'),
            'token' => Yii::t('api', 'Token'),
            'created_at' => Yii::t('api', 'Created At'),
            'updated_at' => Yii::t('api', 'Updated At'),
        ];
    }

    public static function lookup($token) {
      // lookup token for user_id
      $ut = UserToken::find()
        ->where(['token'=>$token])
        ->one();
        if (!is_null($ut) && $ut->user->status>= User::STATUS_ACTIVE) {
            return $ut->user_id;
        } else {
          // no user token or user deleted
          return false;
        }
    }
          /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function newtoken($token,$user_id) {
      // replaces user token
      // not sure how this would work

    }

    public static function signupUser($email, $firstname='',$lastname='') {
      $username = $fullname = $firstname.' '.$lastname;
      if ($username == ' ') $username ='ios';
      if (isset($username) && User::find()->where(['username' => $username])->exists()) {
        $username = User::generateUniqueUsername($username,'ios');
      }
      $password = Yii::$app->security->generateRandomString(12);
        $user = new User([
            'username' => $username, // $attributes['login'],
            'email' => $email,
            'password' => $password,
            'status' => User::STATUS_ACTIVE,
        ]);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $transaction = $user->getDb()->beginTransaction();
        if ($user->save()) {
            $ut = new UserToken([
                'user_id' => $user->id,
                'token' => Yii::$app->security->generateRandomString(40),
            ]);
            if ($ut->save()) {
                User::completeInitialize($user->id);
                UserProfile::applySocialNames($user->id,$firstname,$lastname,$fullname);
                $transaction->commit();
                return $user->id;
            } else {
                print_r($auth->getErrors());
            }
        } else {
            $transaction->rollBack();
            print_r($user->getErrors());
        }
    }

}
