<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use frontend\models\Meeting;
use frontend\models\Participant;
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
	const ACTION_REMOVE_PLACE = 13;
	const ACTION_REJECT_PLACE = 15;
	const ACTION_SUGGEST_TIME = 20;
	const ACTION_ACCEPT_ALL_TIMES = 21;
	const ACTION_ACCEPT_TIME = 22;
	const ACTION_REMOVE_TIME = 23;
	const ACTION_REJECT_TIME = 25;
	const ACTION_INVITE_PARTICIPANT = 30;
	const ACTION_SUGGEST_ACTIVITY = 70;
	const ACTION_ACCEPT_ALL_ACTIVITIES =71;
	const ACTION_ACCEPT_ACTIVITY = 72;
	const ACTION_REMOVE_ACTIVITY = 73;
	const ACTION_REJECT_ACTIVITY = 75;
	const ACTION_ADD_NOTE = 40;
	const ACTION_SEND_INVITE = 50;
	const ACTION_SEND_EVERYONE_AVAILABLE = 55;
	const ACTION_FINALIZE_INVITE = 60;
	const ACTION_COMPLETE_MEETING = 100;
	const ACTION_CHOOSE_PLACE = 110;
	const ACTION_CHOOSE_TIME = 120;
	const ACTION_CHOOSE_ACTIVITY = 130;
	const ACTION_SENT_CONTACT_REQUEST = 150;
	const ACTION_SENT_RUNNING_LATE = 160;
	const ACTION_ABANDON_MEETING = 200;
	const ACTION_MAKE_VIRTUAL = 210;
	const ACTION_MAKE_INPERSON = 215;
	const ACTION_SENT_EMAIL_VERIFICATION = 220;
	const ACTION_REOPEN = 230;
	const ACTION_RESCHEDULE = 232;
	const ACTION_REQUEST_CREATE = 240;
	const ACTION_REQUEST_SENT = 242;
	const ACTION_REQUEST_WITHDRAW = 250;
	const ACTION_REQUEST_ORGANIZER_ACCEPT = 260;
	const ACTION_REQUEST_ACCEPT = 265;
	const ACTION_REQUEST_ORGANIZER_REJECT = 270;
	const ACTION_REQUEST_REJECT = 275;
	const ACTION_REQUEST_LIKE = 280;
	const ACTION_REQUEST_DISLIKE = 281;
	const ACTION_REQUEST_NEUTRAL = 282;
	const ACTION_RESEND = 300;
	const ACTION_REPEAT = 310;

	public static $ignorable = [
			MeetingLog::ACTION_SENT_RUNNING_LATE,
			MeetingLog::ACTION_SENT_CONTACT_REQUEST,
			MeetingLog::ACTION_SENT_EMAIL_VERIFICATION,
			MeetingLog::ACTION_FINALIZE_INVITE,
			MeetingLog::ACTION_ABANDON_MEETING,
			MeetingLog::ACTION_COMPLETE_MEETING,
			MeetingLog::ACTION_RESEND,
			MeetingLog::ACTION_REPEAT,
			MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE,
			MeetingLog::ACTION_REQUEST_WITHDRAW,
			MeetingLog::ACTION_REQUEST_CREATE,
			MeetingLog::ACTION_REQUEST_SENT,
			MeetingLog::ACTION_REQUEST_ORGANIZER_ACCEPT,
			MeetingLog::ACTION_REQUEST_ACCEPT,
			MeetingLog::ACTION_REQUEST_ORGANIZER_REJECT,
			MeetingLog::ACTION_REQUEST_REJECT,
			MeetingLog::ACTION_CANCEL_MEETING,
			MeetingLog::ACTION_DELETE_MEETING
		];

	public static $groupSkip=[
		MeetingLog::ACTION_ACCEPT_ALL_PLACES,
		MeetingLog::ACTION_ACCEPT_PLACE,
		MeetingLog::ACTION_REJECT_PLACE,
		MeetingLog::ACTION_ACCEPT_ALL_TIMES,
		MeetingLog::ACTION_ACCEPT_TIME,
		MeetingLog::ACTION_REJECT_TIME,
		MeetingLog::ACTION_ACCEPT_ALL_ACTIVITIES,
		MeetingLog::ACTION_ACCEPT_ACTIVITY,
		MeetingLog::ACTION_REJECT_ACTIVITY
	];
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
				$m=Meeting::findOne($meeting_id);
				if ($action==MeetingLog::ACTION_MAKE_VIRTUAL) {
					if ($m->isVirtual()) {
						// already virtual
						return;
					}
				} else if ($action==MeetingLog::ACTION_MAKE_INPERSON) {
					$m=Meeting::findOne($meeting_id);
					if (!$m->isVirtual()) {
						// already in person
						return;
					}
				}
				if ($actor_id ==0) {
					$actor_id = $m->owner_id;
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
				case MeetingLog::ACTION_SUGGEST_ACTIVITY:
				$label = Yii::t('frontend','added activity');
				break;
				case MeetingLog::ACTION_REMOVE_PLACE:
				$label = Yii::t('frontend','removed place');
				break;
				case MeetingLog::ACTION_REMOVE_TIME:
				$label = Yii::t('frontend','removed time');
				break;
				case MeetingLog::ACTION_REMOVE_ACTIVITY:
				$label = Yii::t('frontend','removed time');
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
				case MeetingLog::ACTION_ACCEPT_ALL_ACTIVITIES:
					$label = Yii::t('frontend','accepted all activities');
				break;
				case MeetingLog::ACTION_ACCEPT_ACTIVITY:
					$label = Yii::t('frontend','accepted ACTIVITY');
				break;
				case MeetingLog::ACTION_REJECT_ACTIVITY:
					$label = Yii::t('frontend','rejected ACTIVITY');
				break;
				case MeetingLog::ACTION_CHOOSE_PLACE:
					$label = Yii::t('frontend','chose place');
				break;
				case MeetingLog::ACTION_CHOOSE_TIME:
					$label = Yii::t('frontend','chose time');
				break;
				case MeetingLog::ACTION_CHOOSE_ACTIVITY:
					$label = Yii::t('frontend','chose activity');
				break;
				case MeetingLog::ACTION_SEND_INVITE:
				$label = Yii::t('frontend','Sent');
				break;
				case MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE:
					$label = Yii::t('frontend','Notify organizers everyone is available');
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
				case MeetingLog::ACTION_REQUEST_CREATE:
					$label = Yii::t('frontend','Requested change');
				break;
				case MeetingLog::ACTION_REQUEST_SENT:
					$label = Yii::t('frontend','Requested change email sent');
				break;
				case MeetingLog::ACTION_REQUEST_ACCEPT:
				$label = Yii::t('frontend','Accepted the requested change');
				break;
				case MeetingLog::ACTION_REQUEST_ORGANIZER_ACCEPT:
				$label = Yii::t('frontend','Organizer accepted the requested change');
				break;
				case MeetingLog::ACTION_REQUEST_WITHDRAW:
				$label = Yii::t('frontend','Withdrew requested change');
				break;
				case MeetingLog::ACTION_REQUEST_ORGANIZER_REJECT:
				$label = Yii::t('frontend','Organizer rejected the requested change');
				break;
				case MeetingLog::ACTION_REQUEST_REJECT:
				$label = Yii::t('frontend','Declined the requested change');
				break;
				case MeetingLog::ACTION_REQUEST_LIKE:
					$label = Yii::t('frontend','likes the suggested change');
				break;
				case MeetingLog::ACTION_REQUEST_DISLIKE:
					$label = Yii::t('frontend','dislikes the suggested change');
				break;
				case MeetingLog::ACTION_REQUEST_NEUTRAL:
					$label = Yii::t('frontend','is neutral about the suggested change');
				break;
				case MeetingLog::ACTION_RESEND:
				$label = Yii::t('frontend','Resent meeting invitation');
				break;
				case MeetingLog::ACTION_REPEAT:
				$label = Yii::t('frontend','Repeat the meeting with future times');
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
				case MeetingLog::ACTION_RESEND:
				case MeetingLog::ACTION_ACCEPT_ALL_PLACES:
				case MeetingLog::ACTION_ACCEPT_ALL_TIMES:
				case MeetingLog::ACTION_ACCEPT_ALL_ACTIVITIES:
				case MeetingLog::ACTION_FINALIZE_INVITE:
				case MeetingLog::ACTION_COMPLETE_MEETING:
				case MeetingLog::ACTION_ABANDON_MEETING:
				case MeetingLog::ACTION_MAKE_VIRTUAL:
				case MeetingLog::ACTION_MAKE_INPERSON:
				case MeetingLog::ACTION_SENT_CONTACT_REQUEST:
				case MeetingLog::ACTION_SENT_RUNNING_LATE:
				case MeetingLog::ACTION_SENT_EMAIL_VERIFICATION:
				case MeetingLog::ACTION_REOPEN:
				case MeetingLog::ACTION_RESCHEDULE:
				case MeetingLog::ACTION_REPEAT:
				case MeetingLog::ACTION_SEND_EVERYONE_AVAILABLE:
					$label = Yii::t('frontend','-');
				break;
				case MeetingLog::ACTION_SEND_INVITE:
				case MeetingLog::ACTION_INVITE_PARTICIPANT:
					if ($this->item_id ==0) {
						// backward log compatibility - previously didn't track recipient of invite
						$label = '-';
					} else {
						$label = MiscHelpers::getDisplayName($this->item_id);
						if (is_null($label)) {
							$label = 'Error - unknown user';
						}
					}
				break;
				case MeetingLog::ACTION_REMOVE_PLACE:
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
				case MeetingLog::ACTION_REMOVE_TIME:
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
				case MeetingLog::ACTION_CHOOSE_ACTIVITY:
				case MeetingLog::ACTION_SUGGEST_ACTIVITY:
				case MeetingLog::ACTION_REMOVE_ACTIVITY:
				case MeetingLog::ACTION_ACCEPT_ACTIVITY:
				case MeetingLog::ACTION_REJECT_ACTIVITY:
					// get the start ACTIVITY
					$ma = MeetingActivity::find()->where(['id'=>$this->item_id])->one();
					if (is_null($ma)) {
						$label = 'Error meeting activity unknown';
					} else {
						$label = $ma->activity;
					}
				break;
				case MeetingLog::ACTION_ADD_NOTE:
					if ($this->item_id ==0) {
						$label = 'note not logged';
					} else {
						$label = '"'.MeetingNote::find()->where(['id'=>$this->item_id])->one()->note.'"';
					}
				break;
				case MeetingLog::ACTION_REQUEST_CREATE:
				case MeetingLog::ACTION_REQUEST_SENT:
				case MeetingLog::ACTION_REQUEST_WITHDRAW:
				case MeetingLog::ACTION_REQUEST_ACCEPT:
				case MeetingLog::ACTION_REQUEST_ORGANIZER_ACCEPT:
				case MeetingLog::ACTION_REQUEST_REJECT:
				case MeetingLog::ACTION_REQUEST_ORGANIZER_REJECT:
				case MeetingLog::ACTION_REQUEST_LIKE:
				case MeetingLog::ACTION_REQUEST_DISLIKE:
				case MeetingLog::ACTION_REQUEST_NEUTRAL:
					$label = \frontend\models\Request::buildSubject($this->item_id);
				break;
				default:
					$label = Yii::t('frontend','n/a');
				break;
			}
			return $label;
		}

		public static function hasEventOccurred($meeting_id,$action) {
			$cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
				->andWhere(['action'=>$action])
				->count();
			if ($cnt>0) {
				return true;
			} else {
				return false;
			}
		}

		public static function getHistory($meeting_id,$user_id,$cleared_at) {
			// build a textual history of events for this meeting
			// not performed by this user_id and since cleared_at
			// first, identify role of user
			$cntActorsMentioned =0;
			$actorsMentioned=[];
			$m=Meeting::findOne($meeting_id);
			$isOrganizer=false;
			if ($m->owner_id == $user_id) {
				$isOrganizer=true;
			} else {
				$p = Participant::find()
					->where(['meeting_id'=>$meeting_id])
					->andWhere(['participant_id'=>$user_id])
					->one();
				if ($p->participant_type == Participant::TYPE_ORGANIZER) {
					$isOrganizer=true;
				}
			}
			// is it a group Meeting
			$isGroup = false;
			$cntP = Participant::find()
				->where(['meeting_id'=>$meeting_id])
				->count();
			if ($cntP>1) {
				$isGroup=true;
			}
			$str ='';
			// find events not created by this person
			$events = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
				->andWhere('actor_id<>'.$user_id)
				->andWhere('created_at>'.$cleared_at)
				->orderBy(['created_at' => SORT_DESC,'actor_id'=>SORT_ASC])->all();
			$num_events = count($events);
			if ($num_events==0) {
				return false;
			}
			$cnt =1;
			$current_actor = 0;
			$current_str='';
			$items_mentioned =[];
			foreach ($events as $e) {
				if ($e->actor_id <> $current_actor) {
					// new actor, update the overall string
					if ($current_str!='') {
						// add line break if not first entry
						$str.=$current_str.'<br />';
					}
					// reset the current actor's event string
					$current_str='';
					$current_actor = $e->actor_id;
					$cntActorsMentioned +=1;
					$actorsMentioned[]=$current_actor;
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
					(in_array($e->action,MeetingLog::$ignorable)) ||
					// skip over availability response events in multi participant meetings
					($isGroup && !$isOrganizer && in_array($e->action,MeetingLog::$groupSkip))
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
			// if this user_id is the only actor mentioned, they don't the email
			if ($cntActorsMentioned==1 && $actorsMentioned[0]==$user_id) {
				return false;
			}
			return $str;
		}

		public static function withinActionLimit($meeting_id,$action,$actor_id,$limit = 7) {
			// how many times can this user perform this action for a meeting
			$cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
				->andWhere(['actor_id'=>$actor_id])
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

		public static function withinActionTimeLimit($meeting_id,$action,$actor_id,$limit = 1, $seconds = 900) {
			// how many times can this user perform this action for a meeting in last period of $seconds
			$cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
				->andWhere(['action'=>$action])
				->andwhere(['actor_id'=>$actor_id])
				->andWhere('created_at>'.(time()-$seconds))
				->count();
			if ($cnt >= $limit ) {
				return false;
			} else {
				return true;
			}
		}


		public static function countAction($meeting_id,$action) {
      // count # of times an action was performed
      $cnt = MeetingLog::find()
				->where(['meeting_id'=>$meeting_id])
        ->andWhere(['action'=>$action])
        ->count();
      return $cnt;
    }
}
