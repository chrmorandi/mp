<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use frontend\models\Meeting;

/**
 * This is the model class for table "meeting_log".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $action
 * @property integer $actor_id
 * @property integer $item_id
 * @property integer $extra_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $actor
 * @property Meeting $meeting
 */
class MeetingLog extends \yii\db\ActiveRecord
{
	const TIMELAPSE = 300; // five minutes

	const ACTION_CREATE_MEETING = 0;
	const ACTION_CANCEL_MEETING = 7;
	const ACTION_DECLINE_MEETING = 9;
	const ACTION_SUGGEST_PLACE = 10;
	const ACTION_ACCEPT_ALL_PLACES = 11;
	const ACTION_ACCEPT_PLACE = 12;
	const ACTION_REJECT_PLACE = 15;
	const ACTION_SUGGEST_TIME = 20;
	const ACTION_ACCEPT_ALL_TIMES = 21;
	const ACTION_ACCEPT_TIME = 22;
	const ACTION_REJECT_TIME = 25;
	const ACTION_INVITE_PARTICIPANT = 30;
	const ACTION_ADD_NOTE = 40;
	const ACTION_SEND_INVITE = 50;
	const ACTION_FINALIZE_INVITE = 60;
	const ACTION_COMPLETE_MEETING = 100;
	const ACTION_CHOOSE_PLACE = 110;
	const ACTION_CHOOSE_TIME = 120;

	// not yet implemented
	//	const ACTION_ = ;
	//	const ACTION_ = ;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'action', 'actor_id', 'item_id', 'extra_id'], 'required'],
            [['meeting_id', 'action', 'actor_id', 'item_id', 'extra_id', 'created_at', 'updated_at'], 'integer']
        ];
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
            'meeting_id' => Yii::t('frontend', 'Meeting ID'),
            'action' => Yii::t('frontend', 'Action'),
            'actor_id' => Yii::t('frontend', 'Actor ID'),
            'item_id' => Yii::t('frontend', 'Item ID'),
            'extra_id' => Yii::t('frontend', 'Extra ID'),
            'created_at' => Yii::t('frontend', 'Created At'),
            'updated_at' => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActor()
    {
        return $this->hasOne(User::className(), ['id' => 'actor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(Meeting::className(), ['id' => 'meeting_id']);
    }

    // add to log
    public static function add($meeting_id,$action,$actor_id=0,$item_id=0,$extra_id=0) {
         $log = new MeetingLog;
         $log->meeting_id=$meeting_id;
         $log->action =$action;
         $log->actor_id =$actor_id;
         $log->item_id =$item_id;
         $log->extra_id =$extra_id;
         $log->save();
				 // sets the touched_at field for the Meeting
				 Meeting::touchLog($meeting_id);
    }

		public function getMeetingLogCommand() {
			switch ($this->action) {
				case MeetingLog::ACTION_CREATE_MEETING:
					$label = Yii::t('frontend','create meeting');
				break;
				case MeetingLog::ACTION_CANCEL_MEETING:
					$label = Yii::t('frontend','cancel meeting');
				break;
				case MeetingLog::ACTION_DECLINE_MEETING:
					$label = Yii::t('frontend','decline meeting');
				break;
				case MeetingLog::ACTION_SUGGEST_PLACE:
				$label = Yii::t('frontend','add place');
				break;
				case MeetingLog::ACTION_SUGGEST_TIME:
				$label = Yii::t('frontend','add time');
				break;
				case MeetingLog::ACTION_ADD_NOTE:
				$label = Yii::t('frontend','add note');
				break;
				case MeetingLog::ACTION_INVITE_PARTICIPANT:
				$label = Yii::t('frontend','Invite participant');
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_PLACES:
					$label = Yii::t('frontend','accept all places');
				break;
				case MeetingLog::ACTION_ACCEPT_PLACE:
					$label = Yii::t('frontend','accept place');
				break;
				case MeetingLog::ACTION_REJECT_PLACE:
					$label = Yii::t('frontend','reject place');
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_TIMES:
					$label = Yii::t('frontend','accept all times');
				break;
				case MeetingLog::ACTION_ACCEPT_TIME:
					$label = Yii::t('frontend','accept time');
				break;
				case MeetingLog::ACTION_REJECT_TIME:
					$label = Yii::t('frontend','reject time');
				break;
				case MeetingLog::ACTION_CHOOSE_PLACE:
					$label = Yii::t('frontend','choose place');
				break;
				case MeetingLog::ACTION_CHOOSE_TIME:
					$label = Yii::t('frontend','choose time');
				break;
				case MeetingLog::ACTION_SEND_INVITE:
				$label = Yii::t('frontend','Send');
				break;
				case MeetingLog::ACTION_FINALIZE_INVITE:
				$label = Yii::t('frontend','Finalize');
				break;
				case MeetingLog::ACTION_COMPLETE_MEETING:
				$label = Yii::t('frontend','Complete meeting');
				break;
				default:
					$label = Yii::t('frontend','Unknown');
				break;
			}
			return $label;
		}

		public function getMeetingLogItem() {
			$label='';
			switch ($this->action) {
				case MeetingLog::ACTION_CREATE_MEETING:
				case MeetingLog::ACTION_CANCEL_MEETING:
				case MeetingLog::ACTION_DECLINE_MEETING:
					$label = Yii::t('frontend','-');
				break;
				case MeetingLog::ACTION_INVITE_PARTICIPANT:
					$label = Yii::t('frontend','Invite participant');
				break;
				case MeetingLog::ACTION_SUGGEST_PLACE:
				case MeetingLog::ACTION_ACCEPT_PLACE:
				case MeetingLog::ACTION_REJECT_PLACE:
				case MeetingLog::ACTION_CHOOSE_PLACE:
				//	$label = MeetingPlace::find()->where(['id'=>$this->item_id])->one()->place->name;
				break;
				case MeetingLog::ACTION_CHOOSE_TIME:
				case MeetingLog::ACTION_SUGGEST_TIME:
				case MeetingLog::ACTION_ACCEPT_TIME:
				case MeetingLog::ACTION_REJECT_TIME:
					// get the start time
					$label = Meeting::friendlyDateFromTimestamp(MeetingTime::find()->where(['id'=>$this->item_id])->one()->start);
				break;
				case MeetingLog::ACTION_ADD_NOTE:
					$label = MeetingNote::find()->where(['id'=>$this->item_id])->one()->note;
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_PLACES:
				case MeetingLog::ACTION_ACCEPT_ALL_TIMES:
				case MeetingLog::ACTION_SEND_INVITE:
				case MeetingLog::ACTION_FINALIZE_INVITE:
				case MeetingLog::ACTION_COMPLETE_MEETING:
				$label = Yii::t('frontend','-');
				break;
				default:
					$label = Yii::t('frontend','n/a');
				break;
			}
			return $label;
		}
}
