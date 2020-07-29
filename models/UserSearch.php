<?php
/**
 *
 * User: ThangDang
 * Date: 7/29/20
 * Time: 15:43
 *
 */

namespace tas\social\models;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User{
	public $keyword;
	
	public function rules(){
		return [
			['keyword','safe'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function scenarios(){
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}
	
	public function search($params){
		$query        = User::find();
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort'  => [
				'defaultOrder' => ['username' => SORT_DESC],
			],
		]);
		
		$this->load($params);
		
		if(!$this->validate()){
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}
		
		// grid filtering conditions
		$query->andFilterWhere([
			'OR',
			['like','username',$this->keyword],
			['like','email',$this->keyword],
			['like','full_name',$this->keyword],
		]);
				
		return $dataProvider;
	}
}