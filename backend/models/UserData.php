<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
use common\models\User;
use frontend\models\Meeting;
use frontend\models\Participant;
use frontend\models\UserPlace;
use frontend\models\Friend;
use frontend\models\Auth;

/**
 * This is the model class for table "user_data".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $is_social
 * @property integer $count_meetings
 * @property integer $count_meetings_last30
 * @property integer $count_meeting_participant
 * @property integer $count_meeting_participant_last30
 * @property integer $count_places
 * @property integer $count_friends
 * @property integer $invite_then_own
 * @property string $domain
 * @property string $domain_ext
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserData extends \yii\db\ActiveRecord
{
    public $cnt;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_data';
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
            [['user_id' ], 'required'],
            [['user_id', 'is_social', 'count_meetings', 'count_meetings_last30', 'count_meeting_participant', 'count_meeting_participant_last30', 'count_places', 'count_friends', 'invite_then_own','created_at', 'updated_at'], 'integer'],
            [['domain','domain_ext'], 'string'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'user_id' => Yii::t('backend', 'UId'),
            'is_social' => Yii::t('backend', 'Is Social?'),
            'count_meetings' => Yii::t('backend', '# Mtgs'),
            'count_meetings_last30' => Yii::t('backend', '# Mtgs L30'),
            'count_meeting_participant' => Yii::t('backend', '# Prtcpnt'),
            'count_meeting_participant_last30' => Yii::t('backend', '# Prtcpnt L30'),
            'count_places' => Yii::t('backend', '# Places'),
            'count_friends' => Yii::t('backend', '# Friends'),
            'invite_then_own'=> Yii::t('backend','Inv>Org'),
            'domain'=> Yii::t('backend','Domain'),
            'domain_ext'=> Yii::t('backend','Ext'),
            'created_at' => Yii::t('backend', 'Created At'),
            'updated_at' => Yii::t('backend', 'Updated At'),
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
     * @return UserDataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserDataQuery(get_called_class());
    }

    public static function reset() {
      UserData::deleteAll();
    }

    public static function loadDomains() {
      $users = User::find()
        ->where('status>0') // not deleted
        ->all();
      foreach ($users as $u) {
        //var_dump(mailparse_rfc822_parse_addresses($u->email));
        if (strstr ( $u->email ,'@' ) === false)  {
          continue;
        }
        $suffix = explode('@',$u->email);
        $domain = $suffix[1];
        $ext =strrchr( $domain ,'.' );
        if ($ext===false) continue;
        $ext = trim($ext,'.');
        echo $domain. ' => '.$ext.'<br />';
        if ($domain<>'') {
          $ud=UserData::find()
            ->where(['user_id'=>$u->id])
            ->one();
            if (!is_null($ud)) {
              $ud->domain = $domain;
              $ud->domain_ext = $ext;
              $ud->update();
            }

        }
      }
    }

    public static function calculate($since=false,$after = 0) {
      if ($since===false) {
        $since = mktime(0, 0, 0);
      }
      $monthago = $since-(60*60*24*30);
      $all = User::find()->where('created_at>'.$after)->andWhere('created_at<'.$since)->all();
      foreach ($all as $u) {
        // create new record for user or update old one
        $ud = UserData::find()->where(['user_id'=>$u->id])->one();
        if (is_null($ud)) {
          $ud = new UserData();
          $ud->user_id = $u->id;
          $ud->save();
        }
        $user_id = $u->id;
        // count meetings they've organized
        $ud->count_meetings = Meeting::find()->where(['owner_id'=>$user_id])->andWhere('created_at<'.$since)->count();
        $ud->count_meetings_last30 = Meeting::find()->where(['owner_id'=>$user_id])->andWhere('created_at<'.$since)->andWhere('created_at>='.$monthago)->count();
        // count meetings they were invited to
        $ud->count_meeting_participant = Participant::find()->where(['participant_id'=>$user_id])->andWhere('created_at<'.$since)->count();
        $ud->count_meeting_participant_last30 = Participant::find()->where(['participant_id'=>$user_id])->andWhere('created_at<'.$since)->andWhere('created_at>='.$monthago)->count();
        // count places and Friends
        $ud->count_places = UserPlace::find()->where(['user_id'=>$user_id])->andWhere('created_at<'.$since)->count();
        $ud->count_friends = Friend::find()->where(['user_id'=>$user_id])->andWhere('created_at<'.$since)->count();
        // calculate invite than Own - participant first, then organizer
        $first_invite = Participant::find()->where(['participant_id'=>$user_id])->andWhere('created_at<'.$since)->orderBy('created_at asc')->one();
        $first_organized = Meeting::find()->where(['owner_id'=>$user_id])->andWhere('created_at<'.$since)->orderBy('created_at asc')->one();
        $ud->invite_then_own =0;
        if (!is_null($first_invite) && !is_null($first_organized)) {
          if ($first_invite->created_at < $first_organized->created_at && $first_organized->created_at < $since) {
            // they were invited as a participant earlier than they organized their own meeting
            $ud->invite_then_own =1;
          }
        }
        if (Auth::find()->where(['user_id'=>$user_id])->count()>0) {
          $ud->is_social =1;
        } else {
          $ud->is_social =0;
        }
        $ud->update();
      }
    }

    public static function fetch() {
      $data = new \stdClass();
      $data->domains =  new ActiveDataProvider([
        'query' => UserData::find()
          ->select(['domain,COUNT(*) AS cnt'])
          ->groupBy(['domain'])
          ->having('COUNT(*)>4')
          ->orderBy('cnt DESC'),
        ]);
        $data->domain_exts =  new ActiveDataProvider([
          'query' => UserData::find()
            ->select(['domain_ext,COUNT(*) AS cnt'])
            //->where('')
            ->groupBy(['domain_ext'])
            ->having('COUNT(*)>4')
            ->orderBy('cnt DESC'),
          ]);
      return $data;
    }
}
