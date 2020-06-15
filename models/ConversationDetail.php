<?php

namespace tas\social\models;

use app\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
	
	const USER_BOT = - 1;
	
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
			'conversation_detail_id' => Yii::t('app','Conversation Detail ID'),
			'conversation_id'        => Yii::t('app','Conversation ID'),
			'msg_id'                 => Yii::t('app','Msg ID'),
			'sender_id'              => Yii::t('app','Sender ID'),
			'content'                => Yii::t('app','Content'),
			'type'                   => Yii::t('app','Type'),
			'created_at'             => Yii::t('app','Created At'),
			'created_time'           => Yii::t('app','Created Time'),
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
		return $this->hasOne(Conversation::className(),['conversation_id' => 'conversation_id']);
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
			'created_at'  => function(self $model){
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
		return $this->hasOne(User::className(),['id' => 'user_id']);
	}
}
