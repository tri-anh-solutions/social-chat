<?php

namespace tas\social\models;

use app\models\User;
use Exception;
use tas\social\models\config\ModuleConfig;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use function get_object_vars;
use function str_replace;
use function time;

/**
 * This is the model class for table "conversation_details".
 *
 * @property integer          $conversation_detail_id
 * @property integer          $conversation_id
 * @property string           $msg_id
 * @property string           $sender_id
 * @property string           $content
 * @property int              $type
 * @property string           $href
 * @property string           $thumb
 * @property string           $description
 * @property string           $sticker_id
 * @property string           $params
 * @property integer          $created_at
 * @property integer          $created_time
 * @property int              $user_id
 *
 * @property  Conversation    $conversation
 * @property \app\models\User $user
 * @property string           $sender_name
 *
 */
class ConversationDetail extends \yii\db\ActiveRecord{
	const TYPE_TEXT = 1;
	const TYPE_IMG  = 2;
	const TYPE_LINK = 3;
	
	const USER_BOT = - 2;
	const USER_HOOK = - 1;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName(){
		return 'conversation_details';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [
			[['conversation_id','created_at','type','created_time'],'integer'],
			[['content'],'string'],
			[['msg_id','sender_name'],'string','max' => 255],
			['user_id','default','value' => 0],
			['sender_id','safe'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels(){
		return [
			'conversation_detail_id' => Yii::t('social','Conversation Detail ID'),
			'conversation_id'        => Yii::t('social','Conversation ID'),
			'msg_id'                 => Yii::t('social','Msg ID'),
			'sender_id'              => Yii::t('social','Sender ID'),
			'content'                => Yii::t('social','Content'),
			'type'                   => Yii::t('social','Type'),
			'created_at'             => Yii::t('social','Created At'),
			'created_time'           => Yii::t('social','Created Time'),
		];
	}
	
	public function beforeSave($insert){
		if(!$this->user_id){
			$this->user_id = Yii::$app->user ? Yii::$app->user->id : 0;
		}
		if($this->user){
			$this->sender_name = $this->user->username;
		}
		
		return parent::beforeSave($insert);
	}
	
	public function getConversation(){
		return $this->hasOne(Conversation::class,['conversation_id' => 'conversation_id']);
	}
	
	public function behaviors(){
		return [
			[
				'class'      => TimestampBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
				],
			],
		];
	}
	
	public function fields(){
		return [
			'conversation_detail_id',
			'conversation_id',
			'sender_id',
			'sender_name',
			'content',
			'type',
			'created_at' => function(self $model){
				return Yii::$app->formatter->asDatetime($model->created_time);
			},
			'href',
			'thumb',
			'description',
			'sticker_id',
			'params',
		];
	}
	
	/**
	 * @return \app\models\User|\yii\db\ActiveQuery
	 */
	public function getUser(){
		return $this->hasOne(User::class,['id' => 'user_id']);
	}
	
	public function afterSave($insert,$changedAttributes){
		parent::afterSave($insert,$changedAttributes);
		Conversation::updateAllCounters([
			'unread_count' => 1,
		],[
			'conversation_id' => $this->conversation_id,
		]);
		if($insert){
			Conversation::updateAll([
				'last_msg_at' => time(),
			],[
				'conversation_id' => $this->conversation_id,
			]);
			
			$moduleConfig = new ModuleConfig();
			
			if($moduleConfig->auto_reply && ($this->user_id == self::USER_HOOK)){
				try{
					$autoReply = AutoReply::findOne(['message' => $this->content]);
					if($autoReply){
						$msg = $autoReply->reply_content;
						$msg = str_replace(['{sender_name}','{receiver_name}'],
							[$this->conversation->sender_name,$this->conversation->receiver_name],
							$msg);
						
						$reply_form                   = new ReplyMessage([
							'type' => $this->type,
						]);
						$reply_form->receiver_id      = $this->conversation->sender_id;
						$reply_form->sender_id        = $this->conversation->receiver_id;
						$reply_form->type             = $this->conversation->type;
						$reply_form->conversations_id = $this->conversation->conversation_id;
						$reply_form->message          = $msg;
						
						Yii::debug(get_object_vars($reply_form));
						if($reply_form->validate() && $reply_form->sendMsg()){
							Yii::info('send success');
						}else{
							Yii::error($reply_form->getFirstErrors(),'social');
						}
					}
				}
				catch(Exception $ex){
					Yii::error($ex,'social');
				}
			}
		}
	}
}
