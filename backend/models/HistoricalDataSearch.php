<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\HistoricalData;

/**
 * HistoricalDataSearch represents the model behind the search form about `backend\models\HistoricalData`.
 */
class HistoricalDataSearch extends HistoricalData
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'date', 'count_users', 'count_meetings_completed', 'count_meetings_planning', 'count_places', 'average_meetings', 'average_friends', 'average_places', 'source_google', 'source_facebook', 'source_linkedin','count_meetings_expired'], 'integer'],
            [['percent_own_meeting', 'percent_own_meeting_last30', 'percent_invited_own_meeting', 'percent_participant','percent_participant_last30'], 'number'],
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
        $query = HistoricalData::find();

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
            'date' => $this->date,
            'percent_own_meeting' => $this->percent_own_meeting,
            'percent_own_meeting_last30' => $this->percent_own_meeting_last30,
            'percent_invited_own_meeting' => $this->percent_invited_own_meeting,
            'percent_participant' => $this->percent_participant,
            'percent_participant_last30' => $this->percent_participant_last30,
            'count_users' => $this->count_users,
            'count_meetings_completed' => $this->count_meetings_completed,
            'count_meetings_planning' => $this->count_meetings_planning,
            'count_meetings_expired' => $this->count_meetings_expired, 
            'count_places' => $this->count_places,
            'average_meetings' => $this->average_meetings,
            'average_friends' => $this->average_friends,
            'average_places' => $this->average_places,
            'source_google' => $this->source_google,
            'source_facebook' => $this->source_facebook,
            'source_linkedin' => $this->source_linkedin,
        ]);

        return $dataProvider;
    }
}
