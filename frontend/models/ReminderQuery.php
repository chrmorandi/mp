<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Reminder]].
 *
 * @see Reminder
 */
class ReminderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Reminder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Reminder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
