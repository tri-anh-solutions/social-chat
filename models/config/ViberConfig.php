<?php
/**
 *
 * User: ThangDang
 * Date: 8/8/18
 * Time: 17:52
 *
 */

namespace tas\social\models\config;

use Yii;

class ViberConfig extends ConfigModel{
	const EVENT_TYPE_DELIVERED            = 'delivered';
	const EVENT_TYPE_SEEN                 = 'seen';
	const EVENT_TYPE_FAILED               = 'failed';
	const EVENT_TYPE_SUBSCRIBED           = 'subscribed';
	const EVENT_TYPE_UNSUBSCRIBED         = 'unsubscribed';
	const EVENT_TYPE_CONVERSATION_STARTED = 'conversation_started';
	
	public $name   = '';
	public $avatar = '';
	public $token  = '';
	
	public $eventTypes;
	public $sendName   = false;
	public $sendPhoto  = false;
	
	public function type(){
		return 'viber';
	}
	
	public function rules(){
		return [
			[['name','token','avatar'],'safe'],
		];
	}
	
	public static function eventTypeLabel($type){
		switch($type){
			case self::EVENT_TYPE_CONVERSATION_STARTED:
				return Yii::t('social.viber','conversation_started');
				break;
			case self::EVENT_TYPE_UNSUBSCRIBED:
				return Yii::t('social.viber','unsubscribed');
				break;
			case self::EVENT_TYPE_SUBSCRIBED:
				return Yii::t('social.viber','subscribed');
				break;
			case self::EVENT_TYPE_FAILED:
				return Yii::t('social.viber','failed');
				break;
			case self::EVENT_TYPE_SEEN:
				return Yii::t('social.viber','seen');
				break;
			case self::EVENT_TYPE_DELIVERED:
				return Yii::t('social.viber','delivered');
				break;
			default:
				return '';
		}
	}
	
	public static function eventTypeLabels(){
		return [
			self::EVENT_TYPE_DELIVERED            => self::eventTypeLabel(self::EVENT_TYPE_DELIVERED),
			self::EVENT_TYPE_SEEN                 => self::eventTypeLabel(self::EVENT_TYPE_SEEN),
			self::EVENT_TYPE_FAILED               => self::eventTypeLabel(self::EVENT_TYPE_FAILED),
			self::EVENT_TYPE_SUBSCRIBED           => self::eventTypeLabel(self::EVENT_TYPE_SUBSCRIBED),
			self::EVENT_TYPE_UNSUBSCRIBED         => self::eventTypeLabel(self::EVENT_TYPE_UNSUBSCRIBED),
			self::EVENT_TYPE_CONVERSATION_STARTED => self::eventTypeLabel(self::EVENT_TYPE_CONVERSATION_STARTED),
		];
	}
}