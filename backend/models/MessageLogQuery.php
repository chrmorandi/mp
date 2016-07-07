<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[MessageLog]].
 *
 * @see MessageLog
 */
class MessageLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MessageLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MessageLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
