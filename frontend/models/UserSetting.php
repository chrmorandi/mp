<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "user_setting".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $filename
 * @property string $avatar
 * @property string $timezone
 * @property integer $reminder_eve
 * @property integer $reminder_hours
 * @property integer $contact_share
 * @property integer $participant_add_place
 * @property integer $participant_add_date_time
 * @property integer $participant_choose_place
 * @property integer $participant_choose_date_time
 * @property integer $participant_finalize
 * @property integer $no_email
 * @property integer $no_newsletter
 * @property integer $no_updates
 * @property integer $has_updated_timezone
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserSetting extends \yii\db\ActiveRecord
{
    const SETTING_NO = 0;
    const SETTING_OFF = 0;
    const SETTING_ON = 1; // for checkbox on
    const SETTING_YES = 10;
    const SETTING_24_HOUR = 24;
    const SETTING_48_HOUR = 48;
    const SETTING_72_HOUR = 72;

    const EMAIL_OK = 0;
    const EMAIL_NONE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_setting';
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
            [['user_id', ], 'required'],
            [['user_id', ], 'unique'],
            [['user_id', 'reminder_eve', 'reminder_hours', 'contact_share', 'no_email', 'created_at', 'updated_at','participant_add_place', 'participant_add_date_time', 'participant_choose_place', 'participant_choose_date_time', 'participant_finalize','no_newsletter','no_updates','has_updated_timezone'], 'integer'],
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
            'filename' => Yii::t('frontend', 'Filename'),
            'avatar' => Yii::t('frontend', 'Avatar'),
            'timezone' => Yii::t('frontend', 'Local Timezone'),
             'has_updated_timezone' => Yii::t('frontend', 'Has Updated Timezone'),
            'reminder_eve' => Yii::t('frontend', 'Reminder Eve'),
           'reminder_hours' => Yii::t('frontend', 'Reminder Hours'),
           'contact_share' => Yii::t('frontend', 'Contact Share'),
            'participant_add_place' => Yii::t('frontend', 'Allow invitees to add place options'),
             'participant_add_date_time' => Yii::t('frontend', 'Allow invitees to add date & time options'),
             'participant_choose_place' => Yii::t('frontend', 'Allow invitees to choose the place'),
             'participant_choose_date_time' => Yii::t('frontend', 'Allow invitees to choose the date & time'),
             'participant_finalize' => Yii::t('frontend', 'Allow invitees to finalize meetings'),
             'no_email' => Yii::t('frontend', 'No Email'),
             'no_newsletter' => Yii::t('frontend', 'No newsletters'),
             'no_updates' => Yii::t('frontend', 'No updates'),
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

    public static function safeGet($user_id) {
      // initialize first, then get
      UserSetting::initialize($user_id);
      // return the UserSetting
      $us = UserSetting::find()->where(['user_id'=>$user_id])->one();
      if (empty($us->timezone)) {
        $us->timezone='America/Los_Angeles';
        $us->update();
      }
      return $us;
    }

    public static function initialize($user_id) {
      $us = UserSetting::find()->where(['user_id'=>$user_id])->one();
      if (is_null($us)) {
        $us=new UserSetting;
        $us->user_id = $user_id;
        $us->filename='';
        $us->timezone='America/Los_Angeles';
        $us->avatar='';
        $us->reminder_eve = self::SETTING_YES;
        $us->no_email = self::SETTING_NO;
        $us->contact_share = self::SETTING_YES;
        $us->reminder_hours = 48;
        $us->no_newsletter = self::SETTING_NO;
        $us->no_updates = self::SETTING_NO;
        $us->participant_add_place = self::SETTING_ON;
        $us->participant_add_date_time = self::SETTING_ON;
        $us->participant_choose_place = self::SETTING_OFF;
        $us->participant_choose_date_time = self::SETTING_OFF;
        $us->participant_finalize = self::SETTING_OFF;
        $us->save();
      }
      return $us->id;
    }

    public function getEarlyReminderType($data) {
        $options = $this->getEarlyReminderOptions();
        return $options[$data];
      }

      public function getEarlyReminderOptions()
      {
        return array(
            self::SETTING_24_HOUR => '24 hours ahead',
            self::SETTING_48_HOUR => '48 hours ahead',
            self::SETTING_72_HOUR => '72 hours ahead',
            self::SETTING_OFF => 'Do not send an early reminder',
           );
       }

       public static function hasUserSetTimezone($user_id) {
         // returns true if user has already configured a timezone
         $us = UserSetting::find()->where(['user_id'=>$user_id])->one();
         if (is_null($us)) {
           UserSetting::initialize($user_id);
           return false;
         } else {
           if ($us->has_updated_timezone == UserSetting::SETTING_ON) {
             return true;
           } else {
             return false;
           }
         }
       }
}
