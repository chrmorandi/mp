<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Daemon]].
 *
 * @see Daemon
 */
class DaemonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Daemon[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Daemon|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
