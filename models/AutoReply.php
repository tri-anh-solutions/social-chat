<?php

namespace tas\social\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%social_auto_reply}}".
 *
 * @property int    $id_social_auto_reply
 * @property string $title
 * @property string $message
 * @property string $reply_content
 * @property int    $created_at
 * @property int    $updated_at
 */
class AutoReply extends \yii\db\ActiveRecord{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return '{{%social_auto_reply}}';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['reply_content'],'string'],
			[['created_at','updated_at'],'integer'],
			[['title','message'],'string','max' => 255],
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id_social_auto_reply' => Yii::t('social','Id'),
			'title'                => Yii::t('social','Title'),
			'message'              => Yii::t('social','Message'),
			'reply_content'        => Yii::t('social','Reply Content'),
			'created_at'           => Yii::t('social','Created At'),
			'updated_at'           => Yii::t('social','Updated At'),
		];
	}
	
	public function behaviors(){
		return [
			TimestampBehavior::className(),
		];
	}
}
