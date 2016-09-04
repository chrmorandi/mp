<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Friend;
use frontend\models\UserProfile;
use yii\db\Query;
use yii\db\Expression;

/**
 * FriendSearch represents the model behind the search form about `frontend\models\Friend`.
 */
class FriendSearch extends Friend
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'friend_id', 'status', 'number_meetings', 'is_favorite', 'created_at', 'updated_at'], 'integer'],
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
        //$query = Friend::find();
$query = (new Query())
    ->select(['friend.id','friend.user_id','user.email','user_profile.fullname',"lower('f') as address_type"])
    ->from('friend')
    ->join('LEFT JOIN', 'user', 'user.id = friend.friend_id')
    ->join('RIGHT JOIN', 'user_profile', 'user_profile.user_id = friend.friend_id')
    ->where(['friend.user_id'=>Yii::$app->user->getId()]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'sort'=> ['defaultOrder' => ['friend.email'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => 7,
                'params' => array_merge($_GET, ['tab' => 'friend']),
              ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'email' => $this->email,
            'fullname' =>$this->fullname,
            'id' => $this->id,
            'user_id' => $this->user_id,
            'friend_id' => $this->friend_id,
            'status' => $this->status,
            'number_meetings' => $this->number_meetings,
            'is_favorite' => $this->is_favorite,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
