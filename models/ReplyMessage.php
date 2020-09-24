<?php
/**
 *
 * User: ThangDang
 * Date: 12/13/17
 * Time: 11:08 PM
 *
 */

namespace tas\social\models;

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
				$msg_id = $this->sendZalo($this->receiver_id,$this->message);
				break;
			case Conversation::TYPE_VIBER:
				$msg_id = $this->sendViver($this->receiver_id,$this->message);
				break;
			case Conversation::TYPE_LHC:
				$msg_id = $this->sendLHC();
				break;
		}
		Yii::debug($msg_id);
		if($msg_id){
			$msg                  = new ConversationDetail();
			$msg->conversation_id = $this->conversations_id;
			$msg->msg_id          = (string) $msg_id;
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
	
	private function sendZalo($receiver,$msg,$type = 'text'){
		$zalo_config = new ConfigZalo();
		
		$oaid      = $zalo_config->oa_id;
		$data      = json_encode([
			'uid'     => $receiver,
			'message' => $msg,
		]);
		$timestamp = round(microtime(true) * 1000);
		$mac       = hash('sha256',$oaid . $data . $timestamp . $zalo_config->oa_secret);
		
		$params = [
			'oaid'      => $oaid,
			'data'      => $data,
			'timestamp' => $timestamp,
			'mac'       => $mac,
		];
		\Yii::trace($params);
		
		$json_data = json_encode($data);
		$ch        = curl_init('https://openapi.zaloapp.com/oa/v1/sendmessage/text');
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"POST");
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($params));
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		$response = curl_exec($ch);
		$response = json_decode($response);
		\Yii::trace($response);
		
		return isset($response->data) ? $response->data->msgId : false;
	}
	
	private function sendViver($receiver,$msg,$type = 'text'){
		$config = new ViberConfig();
		$data   = [
			'receiver'        => $receiver,
			'min_api_version' => 1,
			'sender'          => [
				'name'   => $config->name,
				'avatar' => $config->avatar,
			],
			'type'            => $type,
			'text'            => $msg,
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