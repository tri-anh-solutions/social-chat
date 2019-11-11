<?php
/**
 *
 * User: ThangDang
 * Date: 12/19/17
 * Time: 11:08 PM
 *
 */

namespace tas\social\controllers\hook;


use tas\social\models\config\ConfigZalo;
use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use Yii;
use yii\httpclient\Client;
use yii\rest\Controller;
use yii\web\Response;
use Zalo\Zalo;
use Zalo\ZaloEndpoint;

class ZaloController extends Controller{
	public function init(){
		parent::init();
		Yii::$app->request->parsers = [
			'application/json' => 'yii\web\JsonParser',
		];
	}
	
	/**
	 * @return string
	 */
	public function actionIndex(){
		Yii::$app->response->format = Response::FORMAT_RAW;
		if(!\app\modules\social\components\ZaloHelper::ValidateMacV2()){
			return 'Invalid mac';
		}
		
		$event = \Yii::$app->request->post('event_name');
		
		switch($event){
			case 'user_send_text':
				return $this->saveMsgText();
				break;
			case 'user_send_image';
				return $this->saveMsgImg();
				break;
			default :
				return '';
		}
	}
	
	private function saveMsgText(){
		$fromuid     = Yii::$app->request->post('sender')['id'];
		$receiver_id = Yii::$app->request->post('recipient')['id'];
		$msgid       = Yii::$app->request->post('message')['msg_id'];
		$message     = Yii::$app->request->post('message')['text'];
		$timestamp   = Yii::$app->request->post('timestamp');
		
		$configZalo = new \app\modules\social\models\config\ConfigZalo();
		$params = ['user_id' => $fromuid];
		Yii::debug(json_encode($params));
		$client   = new Client();
		$request  = $client->get('https://openapi.zalo.me/v2.0/oa/getprofile',[
			'access_token' => $configZalo->access_token,
			'data'         => json_encode($params),
		]);
		$response = $request->send();
		Yii::debug($response->content);
		$sender = json_decode($response->content,true);
		if($sender['error'] != 0){
			Yii::error($sender);
			return '';
		}
		Yii::debug($sender);
		/** @var \app\modules\social\models\Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation               = new Conversation();
			$conversation->unread_count = 1;
		}else{
			$conversation->updateCounters(['unread_count' => 1]);
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender['data']['display_name'];
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			$msg                  = new \app\modules\social\models\ConversationDetail();
			$msg->conversation_id = $conversation->conversation_id;
			$msg->sender_id       = $fromuid;
			$msg->msg_id          = $msgid;
			$msg->content         = $message;
			$msg->created_time    = round($timestamp / 1000);
			$msg->type            = ConversationDetail::TYPE_TEXT;
			if(!$msg->save()){
				\Yii::trace($msg->errors);
			}
		}else{
			\Yii::trace($conversation->errors);
		}
		
		return 'OK';
	}
	
	private function saveMsgImg(){
		$fromuid     = Yii::$app->request->get('fromuid');
		$message     = Yii::$app->request->get('message');
		$receiver_id = Yii::$app->request->get('oaid');
		$msgid       = Yii::$app->request->get('msgid');
		$timestamp   = Yii::$app->request->get('timestamp');
		$href        = Yii::$app->request->get('href');
		$thumb       = Yii::$app->request->get('thumb');
		
		$zalo_config = new ConfigZalo();
		$zalo        = new Zalo(get_object_vars($zalo_config));
		$params      = ['uid' => $fromuid];
		$response    = $zalo->get(ZaloEndpoint::API_OA_GET_PROFILE,$params);
		$sender      = $response->getDecodedBody();
		\Yii::trace($sender);
		
		/** @var Conversation $conversation */
		$conversation = Conversation::findOne(['sender_id' => $fromuid,'type' => Conversation::TYPE_ZALO]);
		if($conversation == null){
			$conversation               = new Conversation();
			$conversation->unread_count = 1;
		}else{
			$conversation->updateCounters(['unread_count' => 1]);
		}
		
		$conversation->sender_id     = $fromuid;
		$conversation->sender_name   = $sender['data']['displayName'];
		$conversation->receiver_id   = $receiver_id;
		$conversation->receiver_name = 'me';
		$conversation->type          = Conversation::TYPE_ZALO;
		
		if($conversation->save()){
			$msg                  = new ConversationDetail();
			$msg->conversation_id = $conversation->conversation_id;
			$msg->sender_id       = $fromuid;
			$msg->msg_id          = $msgid;
			$msg->content         = $message;
			$msg->href            = $href;
			$msg->thumb           = $thumb;
			$msg->created_time    = round($timestamp / 1000);
			$msg->type            = ConversationDetail::TYPE_IMG;
			if(!$msg->save()){
				\Yii::trace($msg->errors);
			}
		}else{
			\Yii::trace($conversation->errors);
		}
		
		return '';
	}
}