<?php

namespace tas\social\controllers\hook;


use tas\social\models\config\ConfigLHC;
use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use Yii;
use yii\rest\Controller;
use yii\web\JsonParser;
use yii\web\Response;
use function json_decode;
use function time;

class LiveChatController extends Controller{
	/** @var ConfigLHC */
	private $_config;
	
	public function init(){
		parent::init();
		Yii::$app->request->parsers = [
			'application/json' => JsonParser::class,
		];
		$this->_config              = new ConfigLHC();
	}
	
	/**
	 * @param $action
	 *
	 * @return bool
	 * @throws \yii\web\BadRequestHttpException
	 */
	public function beforeAction($action){
		if(Yii::$app->request->get('token') != $this->_config->verify_token){
			return false;
		}
		
		return parent::beforeAction($action);
	}
	
	public function actionIndex(){
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		$data                       = json_decode(Yii::$app->request->rawBody,true);
		Yii::debug($data);
		
		$conversation = Conversation::findOne(['sender_id' => $data['chat_id'],'type' => Conversation::TYPE_LHC]);
		if(!$conversation){
			$conversation = Conversation::findOne(['email' => $data['email'],'phone' => $data['phone'],'type' => Conversation::TYPE_LHC]);
		}
		
		$senderName = 'Unknown';
		
		if(!empty($data['full_name'])){
			$senderName = $data['full_name'];
		}elseif(!empty($data['phone'])){
			$senderName = $data['phone'];
		}elseif(!empty($data['email'])){
			$senderName = $data['email'];
		}
		
		if($conversation == null){
			$conversation = new Conversation([
				'type'         => Conversation::TYPE_LHC,
				'sender_id'    => $data['chat_id'],
				'unread_count' => 1,
			]);
		}
		$conversation->sender_name = $senderName;
		$conversation->email       = $data['email'] ?? null;
		$conversation->phone       = $data['phone'] ?? null;
		
		if($conversation->save()){
			$msg = new ConversationDetail([
				'conversation_id' => $conversation->conversation_id,
				'sender_id'       => $conversation->sender_id,
				'created_time'    => time(),
				'user_id'         => - 1,
				'content'         => $data['msg'],
				'type'            => ConversationDetail::TYPE_TEXT,
			]);
			if($msg->save()){
				Yii::info('Save MSG success',self::class);
				//$reply_form                   = new ReplyMessage();
				//$reply_form->receiver_id      = $data['chat_id'];
				//$reply_form->sender_id        = $conversation->receiver_id;
				//$reply_form->type             = Conversation::TYPE_LHC;
				//$reply_form->conversations_id = $conversation->conversation_id;
				//$reply_form->message          = "Reply -> {$msg->content}";
				//
				//Yii::debug(get_object_vars($reply_form));
				//if($reply_form->validate() && $reply_form->sendMsg()){
				//	Yii::debug('send success');
				//}else{
				//	Yii::error($reply_form->getFirstErrors(),'social');
				//}
			}else{
				Yii::error($msg->getFirstErrors());
			}
			
		}else{
			Yii::error($conversation->getFirstErrors());
		}
		
		exit();
	}
}