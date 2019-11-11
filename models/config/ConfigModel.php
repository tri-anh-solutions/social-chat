<?php
/**
 *
 * User: ThangDang
 * Date: 7/27/18
 * Time: 16:45
 *
 */

namespace tas\social\models\config;

use tas\social\models\SocialConfig;
use Yii;
use yii\base\Model;

/**
 *
 * @property string $type
 */
abstract class ConfigModel extends Model{
	/**
	 * @return string
	 */
	abstract public function type();
	
	/**
	 *
	 */
	public function init(){
		parent::init();
		$object_vars = get_object_vars($this);
		if(count($object_vars)){
			$keys = array_keys($object_vars);
			foreach($keys as $key){
				$val = $this->getConfig($key);
				if(!empty($val)){
					$this->{$key} = $val;
				}
			}
		}
	}
	
	/**
	 * @param $key
	 *
	 * @return string
	 */
	private function getConfig($key){
		$cache     = Yii::$app->cache;
		$cache_key = 'config_' . $this->type . '_' . $key;
		if(($result = $cache->get($cache_key)) == false){
			$result = SocialConfig::findOne([
				'key'  => $key,
				'type' => $this->type(),
			]);
			$cache->set($cache_key,$result);
		}
		
		return $result ? $result->value : '';
	}
	
	/**
	 * @param $key
	 * @param $val
	 *
	 * @return bool
	 */
	private function saveConfig($key,$val){
		$result = SocialConfig::findOne([
			'key'  => $key,
			'type' => $this->type(),
		]);
		if($result == null){
			$result       = new SocialConfig();
			$result->type = $this->type();
			$result->key  = $key;
			//$result->title = $this->getAttributeLabel($key);
		}
		$result->value = $val;
		if($result->save()){
			$cache     = Yii::$app->cache;
			$cache_key = 'config_' . $this->type . '_' . $key;
			$cache->delete($cache_key);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Update click to call config
	 *
	 * @return true|false
	 */
	public function update(){
		if($this->validate()){
			$result      = true;
			$object_vars = get_object_vars($this);
			foreach($object_vars as $key => $val){
				$result &= $this->saveConfig($key,$val);
			}
			
			return $result;
		}
		
		return false;
	}
	
	/**
	 * @return string
	 */
	public function getType(){
		return $this->type();
	}
}