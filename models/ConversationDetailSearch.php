<?php
/**
 * Created by PhpStorm.
 * User: tamtk92
 * Date: 10/24/18
 * Time: 10:49 PM
 */

namespace tas\social\models;


use const SORT_DESC;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ConversationDetailSearch extends ConversationDetail
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['conversation_id','created_at','type','created_time'],'integer'],
            [['content'],'string'],
            [['msg_id','sender_id',],'string','max' => 255],
            ['user_id','default','value' => 0],
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
        $query = ConversationDetail::find();
        
        // add conditions that should always apply here
        
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => 7,
            ],
            'sort'       => [
                'defaultOrder' => ['conversation_detail_id' => SORT_DESC],
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
            'conversation_id' => $this->conversation_id,
        ]);
        
        
        return $dataProvider;
    }
}