<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\MeetingReminder;

/**
 * MeetingReminderSearch represents the model behind the search form about `frontend\models\MeetingReminder`.
 */
class MeetingReminderSearch extends MeetingReminder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'meeting_id', 'reminder_id', 'user_id', 'due_at', 'status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MeetingReminder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'meeting_id' => $this->meeting_id,
            'reminder_id' => $this->reminder_id,
            'user_id' => $this->user_id,
            'due_at' => $this->due_at,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
