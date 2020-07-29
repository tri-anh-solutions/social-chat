<?php
/**
 *
 * User: ThangDang
 * Date: 7/29/20
 * Time: 15:43
 *
 */

namespace social\models;


use app\models\User;
use yii\base\Model;

class UserSearch extends User{
	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}
	
	public function search($params){
		$query = User::find();
		
	}
}