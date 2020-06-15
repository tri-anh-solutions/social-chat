<?php
/**
 * Created by PhpStorm.
 * User: T�m
 * Date: 7/23/2015
 * Time: 8:32 PM
 */

namespace tas\social\models\config;

class  ConfigLHC extends ConfigModel{
	public $verify_token;
	public $token;
	public $username;
	public $from_user;
	public $callback_url;
	
	public function type(){
		return 'lhc';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [
			[['token','callback_url','verify_token','from_user','username'],'filter','filter' => 'trim'],
			[['token','callback_url','verify_token','from_user','username'],'required'],
		];
	}
}