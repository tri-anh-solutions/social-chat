<?php
/**
 *
 * User: ThangDang
 * Date: 4/8/20
 * Time: 01:06
 *
 */

namespace tas\social\models;


use app\models\User;
use yii\base\Model;

class UserTransferForm extends Model{
	public $id;
	public $id_user;
	
	public function rules(){
		return [
			[['id','id_user'],'required'],
			[['id','id_user'],'integer'],
			['id','exist','targetClass' => Conversation::class,'targetAttribute' => 'conversation_id'],
			['id_user','exist','targetClass' => User::class,'targetAttribute' => 'id'],
		];
	}
	
	public function save(){
		if($this->validate()){
			$conversation = Conversation::findOne($this->id);
			if($conversation){
				$conversation->locked_by = $this->id_user;
				
				return $conversation->save();
			}
		}
		
		return false;
	}
}