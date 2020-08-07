<?php

namespace tas\social\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%social_config}}".
 *
 * @property int    $id_social_config
 * @property string $type
 * @property string $key
 * @property string $value
 * @property int    $created_at
 * @property int    $updated_at
 */
class SocialConfig extends ActiveRecord{
	/**
	 * {@inheritdoc}
	 */
	public static function tableName(){
		return '{{%social_config}}';
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function rules(){
		return [
			[['type','key'],'required'],
			[['value'],'string'],
			[['created_at','updated_at'],'integer'],
			[['type','key'],'string','max' => 255],
			[['type','key'],'unique','targetAttribute' => ['type','key']],
		];
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(){
		return [
			'id_social_config' => Yii::t('social','Id'),
			'type'             => Yii::t('social','Type'),
			'key'              => Yii::t('social','Key'),
			'value'            => Yii::t('social','Value'),
			'created_at'       => Yii::t('social','Created At'),
			'updated_at'       => Yii::t('social','Updated At'),
		];
	}
	
	public function behaviors(){
		return [
			TimestampBehavior::class,
		];
	}
}
