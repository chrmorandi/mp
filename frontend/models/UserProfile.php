<?php

namespace frontend\models;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use common\models\User;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $firstname
 * @property string $lastname
 * @property string $fullname
 * @property string $filename
 * @property string $avatar
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserProfile extends \yii\db\ActiveRecord
{

  public $image;
  public $username;
  public $tab;
  public $up_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profile';
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
            'user_id' => Yii::t('frontend', 'User ID'),
            'firstname' => Yii::t('frontend', 'First name'),
            'lastname' => Yii::t('frontend', 'Last name'),
            'fullname' => Yii::t('frontend', 'Full name'),
            'filename' => Yii::t('frontend', 'Filename'),
            'avatar' => Yii::t('frontend', 'Avatar'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'], // 'filename', 'avatar','firstname', 'lastname', 'fullname'
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['username', 'validateUsername', 'message'=>Yii::t('frontend','This username already exists.')],
            ['username', 'noSpaces'],
            [['firstname', 'lastname', 'fullname', 'username', 'filename', 'avatar'], 'string', 'max' => 255],
            [['image'], 'safe'],
            [['image'], 'file', 'extensions'=>'jpg, gif, png,jpeg'],
            [['image'], 'file', 'maxSize'=>'500000'],
            ['image', 'image', 'extensions' => 'png, jpg, gif, jpeg',
                    'minWidth' => 200, 'maxWidth' => 4000,
                    'minHeight' => 200, 'maxHeight' => 3000,
                ],
             [['filename', 'avatar'], 'string', 'max' => 255],
        ];
    }

    public function validateUsername($attribute, $params)
        {
          // to do - caution - this requires the user be logged in
          // wherever we're initializing this model
          $username = $this->$attribute;
          // workaround for applySocialNames
          if (is_null(Yii::$app->user->getId())) {
            return true;
          }
          $u = User::find()
            ->where(['username'=>$username])
            ->andWhere('id<>'.Yii::$app->user->getId())
            ->one();
          if (is_null($u)) {
            return true;
          } else {
                $this->addError($attribute, 'This username already exists.');
            }
        }

      public function noSpaces($attribute, $params)
      {
        if (stristr($this->$attribute,' ')!==false) {
          $this->addError($attribute, 'Sorry, we do not allow spaces in your username.');
        }
      }


    public static function initialize($user_id) {
      $up = UserProfile::find()->where(['user_id'=>$user_id])->one();

      if (is_null($up)) {
        $u=User::findOne($user_id);
        $up=new UserProfile;
        $up->user_id = $user_id;
        $up->firstname = '';
        $up->lastname = '';
        $up->fullname = '';
        $up->filename='';
        $up->avatar='';
        if (isset($up->username)) {
          $up->username = $u->username;
        }
        $up->save();
      }
      return $up->id;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->fullname) || ($this->fullname == ' ')) {
              $this->fullname = $this->firstname.' '.$this->lastname;
            }
            return true;
        } else {
            return false;
        }
    }

    public function deleteImage($path,$filename) {
        $file = [];
        $file[] = $path.$filename;
        $file[] = $path.'sqr_'.$filename;
        $file[] = $path.'sm_'.$filename;
        foreach ($file as $f) {
          // check if file exists on server
          if (!empty($f) && file_exists($f)) {
            // delete file
            try {
              unlink($f);
            } catch (ErrorException $e) {
                Yii::warning("Unling failure ".$f);
            }
          }
        }
    }

    public static function applySocialNames($user_id,$firstname='',$lastname='',$fullname='') {
      $up = UserProfile::find()->where(['user_id'=>$user_id])->one();
      if (is_null($up)) {
        // initialize profile if not yet exist
        $up_id = UserProfile::initialize($user_id);
        $up=UserProfile::findOne($up_id);
      }
      if (empty($up->firstname)) {
          $up->firstname = $firstname;
      }
      if (empty($up->lastname)) {
          $up->lastname = $lastname;
      }
      if (empty($up->fullname)) {
          $up->fullname = $fullname;
      }
      $up->update();
    }

    public function updateUsername($username,$user_id) {
      $u = User::find()
        ->where(['username'=>$username])
        ->andWhere('id<>'.$user_id)
        ->all();
      if (empty($u)) {
        $u= User::findOne($user_id);
        $u->username=$username;
        $u->update();
      } else {
        return false;
      }
    }

    public static function improve($user_id,$firstname='',$lastname='') {
      // nondestructively adds firstname and last name to user profile if either are empty
      // initially used from participant url join to capture user provided name
      $u = User::find()
        ->where(['id'=>$user_id])
        ->one();
      if (is_null($u)) {
        return false;
      }
      $up = UserProfile::find()->where(['user_id'=>$user_id])->one();
      if (is_null($up)) {
        // initialize profile if not yet exist
        $up_id = UserProfile::initialize($user_id);
        $up=UserProfile::findOne($up_id);
      }
        if ($up->firstname=='') {
          $up->firstname = $firstname;
        }
        if ($up->lastname=='') {
          $up->lastname = $lastname;
        }
        $up->update();
        return true;
    }
}
