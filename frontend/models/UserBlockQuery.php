<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UserBlock]].
 *
 * @see UserBlock
 */
class UserBlockQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserBlock[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserBlock|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
