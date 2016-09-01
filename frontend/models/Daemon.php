<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "daemon".
 *
 * @property integer $id
 * @property integer $action_id
 * @property integer $task_id
 * @property integer $created_at
 */
class Daemon extends \yii\db\ActiveRecord
{

  const ACTION_FREQUENT = 10;
  const ACTION_QUARTER = 20;
  const ACTION_HOURLY = 30;
  const ACTION_OVERNIGHT = 40;
  const ACTION_WEEKLY = 50;

  const TASK_FIND_FRESH = 200;
  const TASK_REMINDER_CHECK = 210;
  const TASK_MAILGUN_PROCESS = 220;
  const TASK_CHECK_PAST = 230;
  const TASK_CHECK_ABANDONED = 240;
  const TASK_CALC_USER_DATA = 250;
  const TASK_CALC_HISTORICAL_DATA = 260;
  const TASK_DO_NOTHING = 270;
  const TASK_RESET = 280;

  public function behaviors()
  {
      return [
          'timestamp' => [
              'class' => 'yii\behaviors\TimestampBehavior',
              'attributes' => [
                  ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
              ],
          ],
      ];
  }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daemon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action_id', 'task_id'], 'required'],
            [['action_id', 'task_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('frontend', 'ID'),
            'action_id' => Yii::t('frontend', 'Action ID'),
            'task_id' => Yii::t('frontend', 'Task ID'),
            'created_at' => Yii::t('frontend', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     * @return DaemonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DaemonQuery(get_called_class());
    }

  public static function add($action_id,$task_id) {
    $d = new Daemon();
    $d->action_id = $action_id;
    $d->task_id = $task_id;
    $d->save();
  }

   public function displayConstant($lookup) {
      $xClass = new \ReflectionClass ( get_class($this));
    	$constants = $xClass->getConstants();
    	$constName = null;
    	foreach ( $constants as $name => $value )
    	{
    		if ($value == $lookup)
    		{
    			return strtolower($name);
    		}
    	}
    }

    public static function reset() {
      // empties daemon table
      Daemon::deleteAll();
    }

}
