<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Impeachment]].
 *
 * @see Impeachment
 */
class ImpeachmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Impeachment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Impeachment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
