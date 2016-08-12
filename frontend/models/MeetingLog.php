<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use frontend\models\Meeting;
use common\models\User;
use common\components\MiscHelpers;

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
	const TIMELAPSE = 180; // three minutes

	const ACTION_CREATE_MEETING = 0;
	const ACTION_EDIT_MEETING = 3;
	const ACTION_CANCEL_MEETING = 7;
	const ACTION_DELETE_MEETING = 8;
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
	const ACTION_SENT_CONTACT_REQUEST = 150;
	const ACTION_SENT_RUNNING_LATE = 160;
	const ACTION_ABANDON_MEETING = 200;
	const ACTION_MAKE_VIRTUAL = 210;
	const ACTION_MAKE_INPERSON = 215;
	const ACTION_SENT_EMAIL_VERIFICATION = 220;
	const ACTION_REOPEN = 230;
	const ACTION_RESCHEDULE = 232;

	public static $ignorable = [
			MeetingLog::ACTION_SENT_RUNNING_LATE,
			MeetingLog::ACTION_SENT_CONTACT_REQUEST,
			MeetingLog::ACTION_SENT_EMAIL_VERIFICATION,
			MeetingLog::ACTION_FINALIZE_INVITE,
			MeetingLog::ACTION_ABANDON_MEETING,
			MeetingLog::ACTION_COMPLETE_MEETING
		];

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
				if ($action==MeetingLog::ACTION_MAKE_VIRTUAL) {
					$m=Meeting::findOne($meeting_id);
					if ($m->meeting_type == Meeting::TYPE_VIRTUAL || $m->meeting_type == Meeting::TYPE_PHONE || $m->meeting_type == Meeting::TYPE_VIDEO) {
						// already virtual
						return;
					}
				} else if ($action==MeetingLog::ACTION_MAKE_INPERSON) {
					$m=Meeting::findOne($meeting_id);
					if ($m->meeting_type != Meeting::TYPE_VIRTUAL && $m->meeting_type != Meeting::TYPE_PHONE && $m->meeting_type != Meeting::TYPE_VIDEO) {
						// already in person
						return;
					}
				}
         $log = new MeetingLog;
         $log->meeting_id=$meeting_id;
         $log->action =$action;
         $log->actor_id =$actor_id;
         $log->item_id =$item_id;
         $log->extra_id =$extra_id;
         $log->save();
				 // don't need the update sent for these actions, so no need to touch logged_at
				 //$ignorable = [MeetingLog::ACTION_SENT_RUNNING_LATE,MeetingLog::ACTION_SENT_CONTACT_REQUEST,MeetingLog::ACTION_SENT_EMAIL_VERIFICATION,MeetingLog::ACTION_FINALIZE_INVITE];
				 if (!in_array($action,MeetingLog::$ignorable)) {
					 // sets the touched_at field for the Meeting
	 				Meeting::touchLog($meeting_id);
				 }
    }

		public function getMeetingLogCommand() {
			switch ($this->action) {
				case MeetingLog::ACTION_CREATE_MEETING:
					$label = Yii::t('frontend','created meeting');
				break;
				case MeetingLog::ACTION_EDIT_MEETING:
					$label = Yii::t('frontend','edited meeting');
				break;
				case MeetingLog::ACTION_CANCEL_MEETING:
					$label = Yii::t('frontend','canceled meeting');
				break;
				case MeetingLog::ACTION_DECLINE_MEETING:
					$label = Yii::t('frontend','declined meeting');
				break;
				case MeetingLog::ACTION_DELETE_MEETING:
					$label = Yii::t('frontend','deleted meeting');
				break;
				case MeetingLog::ACTION_SUGGEST_PLACE:
				$label = Yii::t('frontend','added place');
				break;
				case MeetingLog::ACTION_SUGGEST_TIME:
				$label = Yii::t('frontend','added time');
				break;
				case MeetingLog::ACTION_ADD_NOTE:
				$label = Yii::t('frontend','added note');
				break;
				case MeetingLog::ACTION_INVITE_PARTICIPANT:
				$label = Yii::t('frontend','invited participant');
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_PLACES:
					$label = Yii::t('frontend','accepted all places');
				break;
				case MeetingLog::ACTION_ACCEPT_PLACE:
					$label = Yii::t('frontend','accepted place');
				break;
				case MeetingLog::ACTION_REJECT_PLACE:
					$label = Yii::t('frontend','rejected place');
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_TIMES:
					$label = Yii::t('frontend','accepted all times');
				break;
				case MeetingLog::ACTION_ACCEPT_TIME:
					$label = Yii::t('frontend','accepted time');
				break;
				case MeetingLog::ACTION_REJECT_TIME:
					$label = Yii::t('frontend','rejected time');
				break;
				case MeetingLog::ACTION_CHOOSE_PLACE:
					$label = Yii::t('frontend','chose place');
				break;
				case MeetingLog::ACTION_CHOOSE_TIME:
					$label = Yii::t('frontend','chose time');
				break;
				case MeetingLog::ACTION_SEND_INVITE:
				$label = Yii::t('frontend','Sent');
				break;
				case MeetingLog::ACTION_FINALIZE_INVITE:
				$label = Yii::t('frontend','Finalized');
				break;
				case MeetingLog::ACTION_COMPLETE_MEETING:
				$label = Yii::t('frontend','Completed meeting');
				break;
				case MeetingLog::ACTION_SENT_CONTACT_REQUEST:
				$label = Yii::t('frontend','was sent a request for contact information');
				break;
				case MeetingLog::ACTION_SENT_RUNNING_LATE:
				$label = Yii::t('frontend','Sent running late notification');
				break;
				case MeetingLog::ACTION_ABANDON_MEETING:
				$label = Yii::t('frontend','Abandoned meeting');
				break;
				case MeetingLog::ACTION_MAKE_VIRTUAL:
				$label = Yii::t('frontend','Switched to virtual meeting');
				break;
				case MeetingLog::ACTION_MAKE_INPERSON:
				$label = Yii::t('frontend','Switched to in person meeting');
				break;
				case MeetingLog::ACTION_SENT_EMAIL_VERIFICATION:
					$label = Yii::t('frontend','Sent email verification link');
				break;
				case MeetingLog::ACTION_REOPEN:
				$label = Yii::t('frontend','Reopened the meeting to make changes');
				break;
				case MeetingLog::ACTION_RESCHEDULE:
				$label = Yii::t('frontend','Rescheduled the meeting');
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
				case MeetingLog::ACTION_EDIT_MEETING:
				case MeetingLog::ACTION_CANCEL_MEETING:
				case MeetingLog::ACTION_DECLINE_MEETING:
					$label = Yii::t('frontend','-');
				break;
				case MeetingLog::ACTION_INVITE_PARTICIPANT:
					$label = MiscHelpers::getDisplayName($this->item_id);
					if (is_null($label)) {
						$label = 'Error - unknown user';
					}
				break;
				case MeetingLog::ACTION_SUGGEST_PLACE:
				$label = Place::find()->where(['id'=>$this->item_id])->one();
				if (is_null($label)) {
					$label = 'Error - suggested unknown place';
				} else {
					$label = $label->name;
					if (is_null($label)) {
						$label = 'Error - suggested place has unknown name';
					}
				}
				break;
				case MeetingLog::ACTION_ACCEPT_PLACE:
				case MeetingLog::ACTION_REJECT_PLACE:
				$label = MeetingPlace::find()->where(['id'=>$this->item_id])->one();
				if (is_null($label)) {
					$label = 'Error - Accept or reject unknown place x1';
				} else {
					if (is_null($label->place))
						$label = 'Error Accept or reject unknown place x2';
					else {
						$label = $label->place->name;
						if (is_null($label)) {
							$label = 'Error accept or reject unknown place name x3';
						}
					}
				}
				break;
				case MeetingLog::ACTION_CHOOSE_PLACE:
				$label = MeetingPlace::find()->where(['id'=>$this->item_id])->one();
				if (is_null($label)) {
					$label = 'Error - chose unknown place x1';
				} else {
					if (is_null($label->place))
						$label = 'Error chose unknown place x2';
					else {
						$label = $label->place->name;
						if (is_null($label)) {
							$label = 'Error - choose unknown place name x3';
						}
					}
				}
				break;
				case MeetingLog::ACTION_CHOOSE_TIME:
				case MeetingLog::ACTION_SUGGEST_TIME:
				case MeetingLog::ACTION_ACCEPT_TIME:
				case MeetingLog::ACTION_REJECT_TIME:
					// get the start time
					$mt = MeetingTime::find()->where(['id'=>$this->item_id])->one();
					if (is_null($mt)) {
						$label = 'Error meeting time unknown';
					} else {
						$label = Meeting::friendlyDateFromTimestamp($mt->start);
					}
				break;
				case MeetingLog::ACTION_ADD_NOTE:
					if ($this->item_id ==0) {
						$label = 'note not logged';
					} else {
						$label = '"'.MeetingNote::find()->where(['id'=>$this->item_id])->one()->note.'"';
					}
				break;
				case MeetingLog::ACTION_ACCEPT_ALL_PLACES:
				case MeetingLog::ACTION_ACCEPT_ALL_TIMES:
				case MeetingLog::ACTION_SEND_INVITE:
				case MeetingLog::ACTION_FINALIZE_INVITE:
				case MeetingLog::ACTION_COMPLETE_MEETING:
				case MeetingLog::ACTION_ABANDON_MEETING:
				case MeetingLog::ACTION_MAKE_VIRTUAL:
				case MeetingLog::ACTION_MAKE_INPERSON:
				case MeetingLog::ACTION_SENT_CONTACT_REQUEST:
				case MeetingLog::ACTION_SENT_RUNNING_LATE:
				case MeetingLog::ACTION_SENT_EMAIL_VERIFICATION:
				case MeetingLog::ACTION_REOPEN:
				case MeetingLog::ACTION_RESCHEDULED:
					$label = Yii::t('frontend','-');
				break;
				default:
					$label = Yii::t('frontend','n/a');
				break;
			}
			return $label;
		}

		public static function getHistory($meeting_id,$user_id,$cleared_at) {
			// build a textual history of events for this meeting
			// not performed by this user_id and since cleared_at
			$str ='';
			$events = MeetingLog::find()->where(['meeting_id'=>$meeting_id])
				->andWhere('actor_id<>'.$user_id)
				->andWhere('created_at>'.$cleared_at)
				->orderBy(['created_at' => SORT_DESC,'actor_id'=>SORT_ASC])->all();
			$num_events = count($events);
			$cnt =1;
			$current_actor = 0;
			$current_str='';
			$items_mentioned =[];
			foreach ($events as $e) {
				if ($e->actor_id <> $current_actor) {
					// new actor, update the overall string
					$str.=$current_str.'<br />';
					// reset the current actor's event string
					$current_str='';
					$current_actor = $e->actor_id;
					$actor = MiscHelpers::getDisplayName($e->actor_id);
				} else {
						$actor = '';
				}
				$action = $e->getMeetingLogCommand();
				$item = $e->getMeetingLogItem();
				// check if action can be skipped
				if (
					// only mention item the first time it appears (last action, as sorted)
					(in_array($e->item_id,$items_mentioned)) ||
					// check if this participant was invited
					($e->action == MeetingLog::ACTION_INVITE_PARTICIPANT && $e->item_id == $user_id) ||
					// check if it was finalized, meaning a confirmation will appear next
					(in_array($e->action,MeetingLog::$ignorable))
					) {
						$num_events-=1; // skip event, reduce number of events
						continue;
					}
				// add event to string
				$items_mentioned[]=$e->item_id;
				if ($actor=='') {
						if ($cnt == $num_events) {
							$current_str.=' and '.$action.' '.$item;
						} else {
							$current_str.=', '.$action.' '.$item;
						}
				} else {
						$current_str.=$actor.' '.$action.' '.$item;
				}
				$cnt+=1;
			}
			// add last current_str (may be empty)
			$str.=$current_str.'<br />';
			if (count($items_mentioned)==0) {
				$str='';
			}
			return $str;
		}

		public static function withinActionLimit($meeting_id,$action,$actor_id,$limit = 7) {
			// how many times can this user perform this action for a meeting
			$cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
				->andwhere(['actor_id'=>$actor_id])
				->andwhere(['action'=>$action])
				->count();
			if ($cnt >= $limit ) {
				return false;
			}
			/*
			$cnt = MeetingNote::find()
				->where(['posted_by'=>$user_id])
				->andWhere('created_at>'.(time()-(24*3600)))
				->count();
			if ($cnt >= MeetingNote::DAY_LIMIT ) {
					return false;
			}
			*/
			return true;
		}

		public static function countAction($meeting_id,$action) {
      // count # of times an action was performed
      $cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
        ->andwhere(['action'=>$action])
        ->count();
      return $cnt;
    }
}
