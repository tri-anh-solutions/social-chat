<?php

namespace tas\social\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FacebookPostSearch represents the model behind the search form about `app\models\FacebookPost`.
 */
class FacebookPostSearch extends FacebookPost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['facebook_post_id'], 'integer'],
            [['name', 'create_time', 'post_id'], 'safe'],
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
        $query = FacebookPost::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => [
                'defaultOrder' => [
                    'create_time' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'facebook_post_id' => $this->facebook_post_id,
            'create_time'      => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'post_id', $this->post_id]);

        return $dataProvider;
    }
}
