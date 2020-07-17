<?php
/**
 *
 * User: ThangDang
 * Date: 12/7/17
 * Time: 10:04 PM
 *
 */

namespace tas\social\controllers\hook;

use DateTime;
use Exception;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use tas\social\components\FacebookUserProfile;
use tas\social\models\AutoReply;
use tas\social\models\config\ConfigFacebook;
use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use tas\social\models\FacebookComment;
use tas\social\models\FacebookPost;
use tas\social\models\ReplyMessage;
use tas\social\models\SocialHookRequest;
use Yii;
use yii\rest\Controller;
use yii\web\JsonParser;
use yii\web\Response;
use function hash_equals;
use function hash_hmac;
use function is_array;
use function json_encode;

class FacebookController extends Controller{
	/** @var ConfigFacebook */
	private $_facebook_config;
	
	public function init(){
		parent::init();
		Yii::$app->request->parsers = [
			'application/json' => JsonParser::class,
		];
		$this->_facebook_config     = new ConfigFacebook();
	}
	
	public function actionIndex(){
		if(Yii::$app->request->isPost){
			return $this->HookEvent();
		}
		
		return $this->RegisterHook(Yii::$app->request->get('hub_challenge'),Yii::$app->request->get('hub_verify_token'));
	}
	
	private function RegisterHook($hub_challenge,$hub_verify_token){
		Yii::$app->response->format = Response::FORMAT_RAW;
		if($hub_verify_token == $this->_facebook_config->verify_token){
			return $hub_challenge;
		}
		
		return '';
	}
	
	private function HookEvent(){
		if($this->ValidateXHub()){
			try{
				$data = Yii::$app->request->bodyParams;
				foreach($data['entry'] as $entry){
					Yii::debug($entry);
					if(isset($entry[' '])){
						Yii::debug('messaging');
						foreach($entry['messaging'] as $msg){
							$this->handleMessage($msg);
						}
					}
					if(isset($entry['changes'])){
						Yii::debug('changes');
						foreach($entry['changes'] as $c){
							if($c['field'] == 'feed'){
								$this->handleFeed($c['value']);
							}
						}
					}
				}
			}
			catch(Exception $ex){
				$hookData = new SocialHookRequest([
					'data' => json_encode([
						'header' => Yii::$app->request->headers->toArray(),
						'body'   => Yii::$app->request->bodyParams,
					]),
				]);
				$hookData->save();
			}
		}
	}
	
	private function ValidateXHub(){
		$sign = Yii::$app->request->headers->get('x-hub-signature');
		$raw  = Yii::$app->request->rawBody;
		Yii::debug($sign);
		if(empty($sign)){
			Yii::$app->response->statusCode = 400;
			
			return false;
		}
		list($function,$value) = explode('=',$sign);
		Yii::debug($raw,self::class);
		Yii::debug($this->_facebook_config->app_secret,self::class);
		$hash = hash_hmac($function,$raw,$this->_facebook_config->app_secret);
		
		if(!hash_equals($value,$hash)){
			Yii::error('Invalid sign',self::class);
			Yii::debug('SIGN: ' . $value,self::class);
			Yii::debug('hash_hmac: ' . $hash,self::class);
			Yii::$app->response->statusCode = 400;
			
			return false;
		}
		
		return true;
	}
	
	private function handleFeed($feed){
		$time_post = gmdate('Y-m-d H:i:s',$feed['created_time']);
		
		try{
			$time_post = new \DateTime($time_post,new \DateTimeZone('UTC'));
		}
		catch(Exception $e){
			Yii::error($e);
			$time_post = new DateTime();
		}
		// change the timezone of the object without changing it's time
		$time_post->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
		// format the datetime
		$time_post = $time_post->format('Y-m-d H:i:s');
		
		Yii::debug($feed['item']);
		if($feed['item'] == 'post' || $feed['item'] == 'status'){
			
			$post = FacebookPost::findOne(['post_id' => $feed['post_id']]);
			if($post == null){
				$post = new FacebookPost();
			}
			
			$post->from_name    = $feed['from']['name'];
			$post->from_id      = $feed['from']['id'];
			$post->post_id      = $feed['post_id'];
			$post->created_time = $time_post;
			$post->message      = $feed['message'];
			$post->save();
		}elseif($feed['item'] == 'comment'){
			$comment = FacebookComment::findOne(['comment_id' => $feed['comment_id']]);
			if($comment == null){
				$comment = new FacebookComment();
			}
			
			$comment->id           = $feed['from']['id'];
			$comment->name         = $feed['from']['name'];
			$comment->post_id      = $feed['post_id'];
			$comment->comment_id   = $feed['comment_id'];
			$comment->parent_id    = $feed['parent_id'] ?? '';
			$comment->created_time = $time_post;
			$comment->message      = $feed['message'];
			$comment->save();
		}
	}
	
	
	private function handleMessage($received_message){
		Yii::debug($received_message);
		//$response;
		Yii::debug($received_message['timestamp']);
		
		$time_post = new \DateTime('now',new \DateTimeZone('UTC'));
		$time_post->setTimestamp($received_message['timestamp'] / 1000);
		
		// change the timezone of the object without changing it's time
		$time_post->setTimezone(new \DateTimeZone('Asia/Ho_Chi_Minh'));
		// format the datetime
		// $time_post = $time_post->format('Y-m-d H:i:s');
		$time_post = $time_post->getTimestamp();
		
		
		$sender_id   = $received_message['sender']['id'];
		$receiver_id = $received_message['recipient']['id'];
		
		$sender   = null;
		$receiver = null;
		
		try{
			$fb = new Facebook([
				'app_id'               => $this->_facebook_config->app_id,
				'app_secret'           => $this->_facebook_config->app_secret,
				'default_access_token' => $this->_facebook_config->page_token,
			]);
			//get sender info
			/** @var FacebookUserProfile $sender */
			$sender = json_decode($fb->get($sender_id)->getBody(),false);
			
			$receiver = json_decode($fb->get($receiver_id)->getBody(),false);
		}
		catch(FacebookSDKException $e){
			Yii::error($e,'social');
		}
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $sender_id,'type' => Conversation::TYPE_FACEBOOK]);
		if($conversation == null){
			$conversation               = new Conversation([
				'type'      => Conversation::TYPE_FACEBOOK,
				'sender_id' => $sender_id,
			]);
			$conversation->unread_count = 1;
		}else{
			$conversation->updateCounters(['unread_count' => 1]);
		}
		
		$conversation->sender_name   = $sender ? $sender->first_name . ' ' . $sender->last_name : '';
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = $receiver ? $receiver->name : '';
		
		if($conversation->save()){
			$msg                  = new ConversationDetail();
			$msg->conversation_id = $conversation->conversation_id;
			$msg->sender_id       = $received_message['sender']['id'];
			$msg->msg_id          = $received_message['message']['mid'];
			$msg->created_time    = $time_post;
			$msg->user_id         = - 1;
			
			// TEXT
			if(isset($received_message['message']['text'])){
				$msg->content = $received_message['message']['text'] ?? '';
				$msg->type    = ConversationDetail::TYPE_TEXT;
			}elseif(isset($received_message['message']['attachments']) && is_array($received_message['message']['attachments'])){
				foreach($received_message['message']['attachments'] as $attachment){
					if($attachment['type'] == 'image'){
						$msg->thumb      = $attachment['payload']['url'];
						$msg->sticker_id = $attachment['payload']['sticker_id'] ?? '';
						$msg->type       = ConversationDetail::TYPE_IMG;
					}
					break;
				}
			}
			// $msg->content         = isset($received_message['message']['text']) ? $received_message['message']['text'] : '';
			
			if($msg->save()){
				if($this->_facebook_config->auto_reply){
					$autoReply = AutoReply::findOne(['message' => $msg->content]);
					if($autoReply){
						$msg = $autoReply->reply_content;
						
						$msg = str_replace(['{sender_name}','{receiver_name}'],
							[$conversation->sender_name,$conversation->receiver_name],
							$msg);
						
						$reply_form                   = new ReplyMessage();
						$reply_form->receiver_id      = $conversation->sender_id;
						$reply_form->sender_id        = $conversation->receiver_id;
						$reply_form->type             = $conversation->type;
						$reply_form->conversations_id = $conversation->conversation_id;
						$reply_form->message          = $msg;
						
						Yii::debug(get_object_vars($reply_form));
						if($reply_form->validate() && $reply_form->sendMsg()){
							Yii::debug('send success');
						}else{
							Yii::error($reply_form->getFirstErrors(),'social');
						}
					}
				}
			}else{
				Yii::error($msg->errors);
			}
		}else{
			Yii::error($conversation->errors);
		}
	}
}