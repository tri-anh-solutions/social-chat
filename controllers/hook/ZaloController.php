<?php
/**
 *
 * User: ThangDang
 * Date: 12/19/17
 * Time: 11:08 PM
 *
 */

namespace tas\social\controllers\hook;


use tas\social\components\ZaloEventType;
use tas\social\components\ZaloHelper;
use tas\social\models\config\ConfigZalo;
use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use Yii;
use yii\httpclient\Client;
use yii\rest\Controller;
use yii\web\JsonParser;
use yii\web\Response;

class ZaloController extends Controller{
	public function init(){
		parent::init();
		Yii::$app->request->parsers = [
			'application/json' => JsonParser::class,
		];
	}
	
	/**
	 * @return string
	 */
	public function actionIndex(){
		Yii::$app->response->format = Response::FORMAT_RAW;
		if(!ZaloHelper::ValidateMacV2()){
			return 'Invalid mac';
		}
		
		$event = Yii::$app->request->post('event_name');
		
		switch($event){
			case ZaloEventType::SEND_MSG:
				return $this->saveMsgText();
				break;
			case ZaloEventType::SEND_IMAGE_MSG;
				return $this->saveMsgImg();
				break;
			case ZaloEventType::SEND_STICKER_MSG;
				return $this->saveMsgSticker();
				break;
			case ZaloEventType::SEND_LINK_MSG;
				return $this->saveMsgLink();
				break;
			default :
				return '';
		}
	}
	
	private function getUserInfo($id){
		$configZalo = new ConfigZalo();
		$params     = ['user_id' => $id];
		$client     = new Client();
		$request    = $client->get('https://openapi.zalo.me/v2.0/oa/getprofile',[
			'access_token' => $configZalo->access_token,
			'data'         => json_encode($params),
		]);
		$response   = $request->send();
		Yii::debug($response->content);
		$sender = json_decode($response->content,true);
		if($sender['error'] != 0){
			Yii::error($sender);
			
			return false;
		}
		Yii::debug($sender);
		
		return $sender['data']['display_name'] ?? false;
	}
	
	
	private function saveMsgText(){
		$fromuid     = Yii::$app->request->post('sender')['id'];
		$receiver_id = Yii::$app->request->post('recipient')['id'];
		$msgid       = Yii::$app->request->post('message')['msg_id'];
		$message     = Yii::$app->request->post('message')['text'];
		$timestamp   = Yii::$app->request->post('timestamp');
		
		$sender = $this->getUserInfo($fromuid);
		if(!$sender){
			return '';
		}
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation = new Conversation();
			//$conversation->unread_count = 1;
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender;
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			$msg                  = new ConversationDetail();
			$msg->conversation_id = $conversation->conversation_id;
			$msg->sender_id       = $fromuid;
			$msg->msg_id          = $msgid;
			$msg->content         = $message;
			$msg->user_id         = ConversationDetail::USER_HOOK;
			$msg->created_time    = round($timestamp / 1000);
			$msg->type            = ConversationDetail::TYPE_TEXT;
			$msg->user_id         = ConversationDetail::USER_HOOK;
			if(!$msg->save()){
				Yii::error($msg->errors);
			}
		}else{
			Yii::error($conversation->errors);
		}
		
		return 'OK';
	}
	
	private function saveMsgImg(){
		$fromuid     = Yii::$app->request->post('sender')['id'];
		$receiver_id = Yii::$app->request->post('recipient')['id'];
		$msgid       = Yii::$app->request->post('message')['msg_id'];
		$message     = Yii::$app->request->post('message')['text'] ?? '';
		$timestamp   = Yii::$app->request->post('timestamp');
		$attachments = Yii::$app->request->post('message')['attachments'] ?? [];
		//$href        = Yii::$app->request->post('href');
		//$thumb       = Yii::$app->request->get('thumb');
		
		$sender = $this->getUserInfo($fromuid);
		if(!$sender){
			return '';
		}
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation = new Conversation();
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender;
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			foreach($attachments as $attachment){
				$msg                  = new ConversationDetail();
				$msg->conversation_id = $conversation->conversation_id;
				$msg->sender_id       = $fromuid;
				$msg->msg_id          = $msgid;
				$msg->content         = $message;
				$msg->href            = $attachment['payload']['url'];
				$msg->thumb           = $attachment['payload']['thumbnail'];
				$msg->created_time    = round($timestamp / 1000);
				$msg->type            = ConversationDetail::TYPE_IMG;
				if(!$msg->save()){
					Yii::error($msg->errors);
				}
			}
			
		}else{
			Yii::error($conversation->errors);
		}
		
		return '';
	}
	
	private function saveMsgSticker(){
		$fromuid     = Yii::$app->request->post('sender')['id'];
		$receiver_id = Yii::$app->request->post('recipient')['id'];
		$msgid       = Yii::$app->request->post('message')['msg_id'];
		$message     = Yii::$app->request->post('message')['text'] ?? '';
		$timestamp   = Yii::$app->request->post('timestamp');
		$attachments = Yii::$app->request->post('message')['attachments'] ?? [];
		//$href        = Yii::$app->request->post('href');
		//$thumb       = Yii::$app->request->get('thumb');
		
		$sender = $this->getUserInfo($fromuid);
		if(!$sender){
			return '';
		}
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation = new Conversation();
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender;
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			foreach($attachments as $attachment){
				$msg                  = new ConversationDetail();
				$msg->conversation_id = $conversation->conversation_id;
				$msg->sender_id       = $fromuid;
				$msg->msg_id          = $msgid;
				$msg->content         = $message;
				$msg->href            = $attachment['payload']['url'];
				$msg->thumb           = $attachment['payload']['url'];
				$msg->sticker_id      = $attachment['payload']['id'];
				$msg->created_time    = round($timestamp / 1000);
				$msg->type            = ConversationDetail::TYPE_IMG;
				if(!$msg->save()){
					Yii::error($msg->errors);
				}
			}
			
		}else{
			Yii::error($conversation->errors);
		}
		
		return '';
	}
	
	private function saveMsgLink(){
		$fromuid     = Yii::$app->request->post('sender')['id'];
		$receiver_id = Yii::$app->request->post('recipient')['id'];
		$msgid       = Yii::$app->request->post('message')['msg_id'];
		$message     = Yii::$app->request->post('message')['text'] ?? '';
		$timestamp   = Yii::$app->request->post('timestamp');
		$attachments = Yii::$app->request->post('message')['attachments'] ?? [];
		
		$sender = $this->getUserInfo($fromuid);
		if(!$sender){
			return '';
		}
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation = new Conversation();
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender;
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			foreach($attachments as $attachment){
				$msg                  = new ConversationDetail();
				$msg->conversation_id = $conversation->conversation_id;
				$msg->sender_id       = $fromuid;
				$msg->msg_id          = $msgid;
				$msg->content         = $message;
				$msg->href            = $attachment['payload']['url'];
				$msg->thumb           = $attachment['payload']['thumbnail'];
				$msg->description     = $attachment['payload']['description'];
				$msg->created_time    = round($timestamp / 1000);
				$msg->type            = ConversationDetail::TYPE_LINK;
				if(!$msg->save()){
					Yii::error($msg->errors);
				}
			}
		}else{
			Yii::error($conversation->errors);
		}
		
		return '';
	}
}