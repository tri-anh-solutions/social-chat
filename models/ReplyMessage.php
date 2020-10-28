<?php
/**
 *
 * User: ThangDang
 * Date: 12/13/17
 * Time: 11:08 PM
 *
 */

namespace tas\social\models;

use Exception;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use tas\social\components\LiveHelperChat;
use tas\social\models\config\ConfigFacebook;
use tas\social\models\config\ConfigLHC;
use tas\social\models\config\ConfigZalo;
use tas\social\models\config\ViberConfig;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\httpclient\Client;
use function json_decode;

class ReplyMessage extends Model{
	public $receiver_id;
	public $sender_id;
	public $message;
	public $conversations_id;
	public $type;
	
	public function rules(){
		return [
			[['receiver_id','message','conversations_id','type'],'required'],
			[['receiver_id','message'],'safe'],
		];
	}
	
	public function sendMsg(){
		$msg_id = false;
		switch($this->type){
			case Conversation::TYPE_FACEBOOK:
				$msg_id = $this->sendFB();
				break;
			case  Conversation::TYPE_ZALO:
				$msg_id = $this->sendZalo();
				break;
			case Conversation::TYPE_VIBER:
				$msg_id = $this->sendViver();
				break;
			case Conversation::TYPE_LHC:
				$msg_id = $this->sendLHC();
				break;
		}
		Yii::debug($msg_id);
		if($msg_id){
			$msg                  = new ConversationDetail();
			$msg->conversation_id = $this->conversations_id;
			$msg->msg_id          = (string)$msg_id;
			$msg->content         = $this->message;
			$msg->created_time    = (string)time();
			$msg->sender_id       = $this->sender_id;
			$msg->user_id         = Yii::$app->user->id ?? 0;
			if(!$msg->save()){
				\Yii::debug($msg->errors);
			}
			
			return $msg;
		}
		
		$this->addError('message','send msg error');
		
		return null;
	}
	
	private function sendFB(){
		$facebook_config = new ConfigFacebook();
		try{
			
			$data = [
				'recipient' => [
					'id' => $this->receiver_id,
				],
				'message'   => [
					'text' => $this->message,
				],
			];
			
			$fb = new Facebook([
				'app_id'     => $facebook_config->app_id,
				'app_secret' => $facebook_config->app_secret,
			]);
			
			if(empty($facebook_config->page_token)){
				Yii::error('empty page token');
				
				return null;
			}
			
			try{
				$tk = !empty($facebook_config->long_page_token) ? $facebook_config->long_page_token : $facebook_config->page_token;
				/** @var \Facebook\FacebookResponse $response */
				$response = $fb->post(
					'/me/messages',
					$data,
					$tk
				);
				Yii::debug($tk);
				Yii::debug($response->getBody());
				$data = json_decode($response->getBody());
				if($data && isset($data->message_id)){
					return $data->message_id;
				}
				
				Yii::error('Parse data error');
				
				return null;
			}
			catch(FacebookSDKException $e){
				Yii::error($e);
				
				return null;
			}
		}
		catch(FacebookSDKException $e){
			Yii::error($e);
			
			return null;
		}
	}
	
	private function sendLHC(){
		$lhc_config = new ConfigLHC();
		try{
			$lhc = new LiveHelperChat();
			try{
				Yii::debug('sender id => ' . $this->receiver_id);
				
				return (string)$lhc->sendMsg($this->receiver_id,$this->message);
			}
			catch(Exception $e){
				Yii::error($e);
				
				return null;
			}
		}
		catch(Exception $e){
			Yii::error($e);
			
			return null;
		}
	}
	
	private function sendZalo($type = 'text'){
		$zalo_config = new ConfigZalo();
		
		$params = [
			'recipient' => [
				'user_id' => $this->receiver_id,
			],
			'message'   => [
				'text' => $this->message,
			],
		];
		
		\Yii::debug($params);
		
		try{
			$client  = new Client([
				'baseUrl' => 'https://openapi.zalo.me/v2.0/oa/message?access_token=' . $zalo_config->access_token,
			]);
			$request = $client->createRequest()
			                  ->setFormat(Client::FORMAT_JSON)
			                  ->setMethod('POST')
			                  ->setData($params);
			
			/** @var \yii\httpclient\Response $response */
			$response = $request->send();
			if($response->isOk){
				Yii::debug($response->content);
				$response_data = json_decode($response->content,true);
				
				return $response_data['data']['message_id'] ?? null;
			}
			
			return null;
		}
		catch(InvalidConfigException $e){
			Yii::error($e);
			
			return null;
		}
	}
	
	private function sendViver($type = 'text'){
		$config = new ViberConfig();
		$data   = [
			'receiver'        => $this->receiver_id,
			'min_api_version' => 1,
			'sender'          => [
				'name'   => $config->name,
				'avatar' => $config->avatar,
			],
			'type'            => $type,
			'text'            => $this->message,
		];
		$client = new Client([
			'baseUrl' => 'https://chatapi.viber.com/pa/send_message',
		]);
		try{
			$request = $client->createRequest()
			                  ->setFormat(Client::FORMAT_JSON)
			                  ->setData($data)
			                  ->setHeaders([
				                  'X-Viber-Auth-Token :' . $config->token,
			                  ]);
			
			/** @var \yii\httpclient\Response $response */
			$response = $request->send();
			if($response->isOk){
				Yii::debug($response->content);
				$response_data = json_decode($response->content,true);
				
				return $response_data['message_token'] ?? null;
			}
			
			return null;
		}
		catch(InvalidConfigException $e){
			Yii::error($e);
			
			return null;
		}
	}
}