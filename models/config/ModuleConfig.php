<?php
/**
 *
 * User: ThangDang
 * Date: 8/7/20
 * Time: 11:04
 *
 */

namespace tas\social\models\config;

class ModuleConfig extends ConfigModel{
	public $auto_reply = false;
	
	public function rules(){
		return [
			['auto_reply','boolean'],
		];
	}
	
	/**
	 * @return string
	 */
	public function type(){
		return 'social';
	}
}