<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Yiigun;
use common\components\MiscHelpers;

/**
 * This is the model class for table "friend".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $friend_id
 * @property integer $status
 * @property integer $number_meetings
 * @property integer $is_favorite
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $friend
 * @property User $user
 */
class Friend extends \yii\db\ActiveRecord
{
    const STATUS_CONNECTED = 0;
    const STATUS_DISCONNECTED = 10;

    const FAVORITE_NO = 0;
    const FAVORITE_YES = 10;

    const NEAR_LIMIT = 25;
    const DAY_LIMIT = 50;

    public $email;
    public $fullname;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'friend';
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
            [['email'], 'email'],
            [['user_id', 'friend_id'], 'required'],
            ['email', 'validateFriendEmail', 'message'=>Yii::t('frontend','You have already added this friend')],
            ['user_id', 'compare','compareAttribute' => 'friend_id', 'operator'=>'!=','message'=>Yii::t('frontend','You can\'t add yourself as a friend')],
            //['email','mailgunValidator'],
            [['user_id', 'friend_id', 'status', 'number_meetings', 'is_favorite', 'created_at', 'updated_at'], 'integer']
        ];
    }

    public function validateFriendEmail($attribute, $params)
        {
          $email = $this->$attribute;
          $u = User::find()->where(['email'=>$email])->one();
          if (empty($u)) {
            return true;
          }
          $f = Friend::find()->where(['user_id'=>Yii::$app->user->getId()])->andWhere(['friend_id'=>$u->id])->one();
          if (!empty($f)) {
                $this->addError($attribute, 'You are already friends with this person.');
            }
        }

    public function scenarios()
    {
        $scenarios = [
            'some_scenario' => ['email'],
        ];

        return array_merge(parent::scenarios(), $scenarios);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'user_id' => Yii::t('frontend', 'User ID'),
            'friend_id' => Yii::t('frontend', 'Friend ID'),
            'status' => Yii::t('frontend', 'Status'),
            'number_meetings' => Yii::t('frontend', 'Number Meetings'),
            'is_favorite' => Yii::t('frontend', 'Is Favorite'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFriend()
    {
        return $this->hasOne(User::className(), ['id' => 'friend_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function getFriendList($user_id) {
      // load user's friends into email list array for autocomplete
      $friend_list = \frontend\models\Friend::find()->where(['user_id' => $user_id])->all();
      $email_list = [];
      foreach ($friend_list as $x) {
        $email_list[] = $x->friend->email;
      }
      return $email_list;
    }

    public static function getDetailedFriendList($user_id) {
      // load user's friends into email list array for autocomplete
      $friends = \frontend\models\Friend::find()->where(['user_id' => $user_id])->all();
      $friend_list = [];
      $cnt=0;
      foreach ($friends as $x) {
        $friend_list[$cnt]['id']=$x->id;
        $friend_list[$cnt]['name']=MiscHelpers::getDisplayName($x->id);
        $friend_list[$cnt]['email']=$x->friend->email;
        $cnt+=1;
      }
      return $friend_list;
    }


    public static function add($user_id,$user_friend_id) {
      // add user_friend_id to user_id list
      // note: user_friend_id is an id from user table
      // check if it is a duplicate
      if (!Friend::find()->where(['user_id'=>$user_id,'friend_id'=>$user_friend_id])->exists()) {
        $f = new Friend();
        $f->user_id = $user_id;
        $f->friend_id = $user_friend_id;
        $f->status=Friend::STATUS_CONNECTED;
        $f->number_meetings =0;
        $f->is_favorite =Friend::FAVORITE_NO;
        $f->save();
      }
    }

    public function mailgunValidator($attribute,$params)
    {
          $yg = new Yiigun('public');
          $result = $yg->validate($this->$attribute);
          if ($result->is_valid)
            return false;
          else {
            $str = 'There is a problem with your email address '.$result->address.'.';
            if ($result->did_you_mean<>'') {
                $str.=' Did you mean '.$result->did_you_mean.'?';
            }
            $this->addError($attribute, $str);
          }
    }

    public static function withinLimit($user_id,$minutes_ago = 180) {
      // how many meetings created by this user in past $minutes_ago
      $cnt = Friend::find()
        ->where(['user_id'=>$user_id])
        ->andWhere('created_at>'.(time()-($minutes_ago*60)))
        ->count();
      if ($cnt >= Friend::NEAR_LIMIT ) {
        return false;
      }
      // check in last DAY_LIMIT
      $cnt = Friend::find()
        ->where(['user_id'=>$user_id])
        ->andWhere('created_at>'.(time()-(24*3600)))
        ->count();
      if ($cnt >= Friend::DAY_LIMIT ) {
          return false;
      }
      return true;
    }
}
