<?php
/**
 * Created by PhpStorm.
 * User: tamtk92
 * Date: 10/24/18
 * Time: 10:49 PM
 */

namespace tas\social\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;

class ConversationSearch extends Conversation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','message_count','unread_count','created_at','updated_at','id_customer'],'integer'],
            [['sender_id','sender_name','receiver_id','receiver_name'],'string','max' => 255],
            ['unread_count','default','value' => 1],
        ];
    }
    
    /**
     * {@inheritdoc}
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
        $query = Conversation::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => 7,
            ],
            'sort'       => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
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
            'id_customer' => $this->id_customer,
        ]);
        
        
        return $dataProvider;
    }
}