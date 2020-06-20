<?php
/**
 *
 * User: ThangDang
 * Date: 4/10/18
 * Time: 23:18
 *
 */

namespace tas\social\controllers;


use app\models\CustomerDetails;
use app\models\CustomerInfo;
use app\models\search\CustomerInfoSearch;
use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use tas\social\models\ReplyMessage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;

/** @noinspection LongInheritanceChainInspection */

class MessengerController extends Controller{
	/**
	 * Lists all Conversation models.
	 *
	 * @return mixed
	 */
	public function actionIndex(){
		return $this->render('index');
	}
	
	public function actionGetConversation($page = 1){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$per_page                   = 200;
		
		$query = Conversation::find()->orderBy(['last_msg_at' => SORT_DESC]);
		
		$provider = new ActiveDataProvider([
			'query'      => $query,
			'pagination' => [
				// 'defaultPageSize' => ,
				'pageSize'     => $per_page,
				'page'         => 0,
				'validatePage' => false,
			],
		]);
		
		return [
			'data'       => $provider->getModels(),
			'total'      => $provider->getPagination()->totalCount,
			'total_page' => $provider->getPagination()->pageCount,
			'page'       => $provider->getPagination()->getPage() + 1,
		];
	}
	
	public function actionGetConversationDetail($id,$page = 1){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$per_page                   = 10;
		Conversation::updateAll(['unread_count' => 0,],['conversation_id' => $id]);
		
		$query = ConversationDetail::find()
		                           ->where([
			                           'conversation_id' => $id,
		                           ]);
		
		$provider = new ActiveDataProvider([
			'query'      => $query,
			'sort'       => [
				'defaultOrder' => [
					'conversation_detail_id' => SORT_DESC,
				],
			],
			'pagination' => [
				'defaultPageSize' => $per_page,
				'page'            => $page - 1,
				'validatePage'    => false,
			],
		]);
		
		return [
			'data'       => $page == 1 ? array_reverse($provider->getModels()) : $provider->getModels(),
			'total'      => $provider->getPagination()->totalCount,
			'total_page' => $provider->getPagination()->pageCount,
			'page'       => $provider->getPagination()->getPage() + 1,
		];
	}
	
	public function actionSendMsg($id,$msg = ''){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$conversation               = Conversation::findOne($id);
		if($conversation == null){
			return [
				'success' => false,
				'error'   => Yii::t('social','Can`\'t find conversation'),
				'data'    => null,
			];
		}
		
		if($conversation->locked_by != Yii::$app->user->id){
			return [
				'success' => false,
				'error'   => Yii::t('social','Conversation locked by {username}',[
					'username' => $conversation->lockedBy->username ?? '',
				]),
				'data'    => null,
			];
		}
		
		$conversation->unread_count = 0;
		$conversation->save();
		
		$reply_form = new ReplyMessage();
		
		$reply_form->receiver_id      = $conversation->sender_id;
		$reply_form->sender_id        = $conversation->receiver_id;
		$reply_form->type             = $conversation->type;
		$reply_form->conversations_id = $id;
		$reply_form->message          = $msg;
		
		
		$status = false;
		$msg    = null;
		$error  = false;
		if($reply_form->validate() && ($msg = $reply_form->sendMsg())){
			$status = true;
		}else{
			$error = $msg ? $msg->getErrors() : Yii::t('social','Send message error');
		}
		
		return [
			'success' => $status,
			'error'   => $error,
			'data'    => $msg,
		];
	}
	
	public function actionSearchCustomers(){
		$response            = [];
		$response['message'] = Yii::t('social','Failed to fetch data.');
		$response['result']  = false;
		if(Yii::$app->request->isAjax){
			$searchModel  = new CustomerInfoSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->get());
			$dataProvider->setPagination(['pageSize' => 10]);
			$response['message'] = Yii::t('social','Successfully fetched data.');
			$response['result']  = true;
			$response['view']    = $this->renderAjax('/customer/index',['searchModel' => $searchModel,'dataProvider' => $dataProvider]);
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		return $response;
	}
	
	public function actionSetCustomer(){
		$response            = [];
		$response['message'] = Yii::t('social','Failed to set customer.');
		$response['result']  = false;
		if(Yii::$app->request->isAjax){
			$conversation_id = Yii::$app->request->post('conversation_id');
			$id_customer     = Yii::$app->request->post('id_customer');
			$customer        = CustomerInfo::findOne($id_customer);
			$customerDetail  = CustomerDetails::findOne($id_customer);
			$conversation    = Conversation::findOne($conversation_id);
			if($conversation){
				$conversation->id_customer = $id_customer;
				if($customerDetail && empty($customerDetail->Email) && !empty($conversation->email)){
					$customerDetail->Email = $conversation->email;
				}
				if(!$customerDetail->save(false)){
					Yii::error($customerDetail->getFirstErrors());
				}
				if($customer && empty($customer->DTDD) && !empty($conversation->phone)){
					$customer->DTDD = $conversation->phone;
				}
				if(!$customer->save(false)){
					Yii::error($customer->getFirstErrors());
				}
				if($conversation->save()){
					$response['message'] = Yii::t('social','Successfully set customer.');
					$response['result']  = true;
				}
			}
			
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		return $response;
	}
	
	public function actionAjaxLock(){
		$response            = [];
		$response['message'] = Yii::t('social','Failed to locked.');
		$response['result']  = false;
		if(Yii::$app->request->isAjax){
			$conversation_id = Yii::$app->request->post('conversation_id');
			$conversation    = Conversation::findOne($conversation_id);
			if($conversation != null){
				$conversation->locked_by = Yii::$app->user->id;
				if($conversation->save()){
					$response['message'] = Yii::t('social','Locked Successfully.');
					$response['result']  = true;
				}
			}
			
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		return $response;
	}
	
	public function actionAjaxUnlock(){
		$response            = [];
		$response['message'] = Yii::t('social','Failed to unlocked.');
		$response['result']  = false;
		if(Yii::$app->request->isAjax){
			$conversation_id = Yii::$app->request->post('conversation_id');
			$conversation    = Conversation::findOne($conversation_id);
			if($conversation != null){
				$conversation->locked_by = null;
				if($conversation->save()){
					$response['message'] = Yii::t('social','Unlocked Successfully.');
					$response['result']  = true;
				}
			}
			
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		return $response;
	}
	
	public function actionUserTransfer(){
		$response            = [];
		$response['message'] = Yii::t('social','Failed to fetch data.');
		$response['result']  = false;
		if(Yii::$app->request->isAjax){
			$searchModel  = new CustomerInfoSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->get());
			$dataProvider->setPagination(['pageSize' => 10]);
			$response['message'] = Yii::t('social','Successfully fetched data.');
			$response['result']  = true;
			$response['view']    = $this->renderAjax('user-transfer',['searchModel' => $searchModel,'dataProvider' => $dataProvider]);
		}
		
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		return $response;
	}
}