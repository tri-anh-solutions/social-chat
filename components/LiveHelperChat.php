<?php
/**
 *
 * User: ThangDang
 * Date: 6/15/20
 * Time: 12:07
 *
 */

namespace tas\social\components;

use Exception;
use tas\social\models\config\ConfigLHC;
use Yii;
use yii\base\BaseObject;
use yii\httpclient\Client;
use function json_decode;
use function trim;

class LiveHelperChat extends BaseObject{
	const ENDPOINT_ADD_MSG = '/restapi/addmsgadmin';
	
	public $url      = '';
	public $username = '';
	public $token    = '';
	
	private $_config;
	public function init(){
		parent::init();
		$this->_config = new ConfigLHC();
		
		$this->url = $this->_config->callback_url;
		$this->username = $this->_config->username;
		$this->token = $this->_config->token;
	}
	
	
	public function sendMsg($chat_id,$content,$sender = 'admin'){
		Yii::debug('chat id -> '.$chat_id,self::class);
		$response = $this->postRequest(self::ENDPOINT_ADD_MSG,[
			'chat_id' => $chat_id,
			'msg'     => $content,
			'sender'  => $sender,
		]);
		if($response && $data = json_decode($response,true)){
			if(!$data['error']){
				return $data['msg']['id'];
			}
			return false;
		}
		
		return false;
	}
	
	/**
	 * @param             $endpoint
	 * @param             $data
	 * @param null|string $file
	 * @param int         $timeout
	 *
	 * @return string|null
	 */
	private function postRequest($endpoint,$data,$file = null,$timeout = 6000){
		try{
			Yii::info('POST: ' . $endpoint);
			$client  = new Client([
				'baseUrl' => $this->url,
			]);
			$request = $client->createRequest();
			$request->setMethod('POST')
				->addHeaders(['Authorization' => 'Basic ' . base64_encode($this->username . ":". $this->token)])
			        ->setOptions([
				        'timeout' => $timeout,
			        ])
			        ->setUrl($endpoint)
			        ->setData($data);
			if($file){
				$request->addFile('file',$file);
			}
			$response = $request->send();
			Yii::info($response->statusCode);
			Yii::info($response->content);
			
			if($response->isOk){
				return trim($response->content);
			}
			
			return null;
		}
		catch(Exception $e){
			Yii::error($e);
			
			return null;
		}
	}
	
	/**
	 * @param string $endpoint
	 * @param array  $data
	 * @param int    $timeout
	 *
	 * @return string|null
	 */
	private function getRequest($endpoint,$data,$timeout = 6000){
		try{
			Yii::info('GET: ' . $endpoint);
			$client  = new Client([
				'baseUrl' => $this->url,
			]);
			$request = $client->createRequest();
			$request->setMethod('GET')
				->addHeaders(['Authorization' => 'Basic ' . base64_encode($this->username . ":". $this->token)])
			        ->setOptions([
				        'timeout' => $timeout,
			        ])
			        ->setUrl($endpoint)
			        ->setData($data);
			$response = $request->send();
			
			Yii::info($response->statusCode);
			Yii::info($response->content);
			
			if($response->isOk){
				Yii::debug($response->content);
				
				return trim($response->content);
			}
			
			return null;
		}
		catch(Exception $e){
			Yii::error($e);
			
			return null;
		}
	}
}