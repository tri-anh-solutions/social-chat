<?php

namespace tas\social\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FaceboookPostSearch represents the model behind the search form about `app\models\FacebookPost`.
 */
class FaceboookPostSearch extends FacebookPost
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['facebook_post_id'], 'integer'],
            [['message', 'from_name', 'from_id', 'created_time', 'updated_time', 'post_id'], 'safe'],
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
            //'key'   => 'encryptId',
            'sort'=> ['defaultOrder' => ['created_time'=>SORT_DESC]]
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
            'created_time'     => $this->created_time,
            'updated_time'     => $this->updated_time,
        ]);
        
        $query->andFilterWhere(['like', 'message', $this->message])
            ->andFilterWhere(['like', 'from_name', $this->from_name])
            ->andFilterWhere(['like', 'from_id', $this->from_id])
            ->andFilterWhere(['like', 'post_id', $this->post_id]);
        
        return $dataProvider;
    }
}
