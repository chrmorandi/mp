<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[HistoricalData]].
 *
 * @see HistoricalData
 */
class HistoricalDataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return HistoricalData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return HistoricalData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
