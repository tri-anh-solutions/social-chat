<?php
/**
 *
 * User: ThangDang
 * Date: 7/24/18
 * Time: 09:50
 *
 */

namespace tas\social\components;


use tas\social\models\config\ConfigZalo;
use Yii;

class ZaloHelper{
	public static function ValidateMac(){
		$zalo_config = new ConfigZalo();
		$event       = Yii::$app->request->get('event');
		$mac         = Yii::$app->request->get('mac');
		$oaid        = Yii::$app->request->get('oaid');
		$appid       = Yii::$app->request->get('appid');
		$timestamp   = Yii::$app->request->get('timestamp');
		$msgid       = Yii::$app->request->get('msgid');
		$fromuid     = Yii::$app->request->get('fromuid');
		$message     = Yii::$app->request->get('message');
		$msginfo     = Yii::$app->request->get('msginfo');
		$href        = Yii::$app->request->get('href');
		$thumb       = Yii::$app->request->get('thumb');
		$description = Yii::$app->request->get('description');
		$params      = Yii::$app->request->get('params');
		$stickerid   = Yii::$app->request->get('stickerid');
		
		switch($event){
			case ZaloEventType::SENDMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $message . $timestamp;
				break;
			case ZaloEventType::SENDIMAGEMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $message . $href . $thumb . $timestamp;
				break;
			case ZaloEventType::SENDLINKMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $message . $href . $thumb . $description . $timestamp;
				break;
			case ZaloEventType::SENDVOICEMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $message . $href . $timestamp;
				break;
			case ZaloEventType::SENDLOCATIONMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $message . $params . $timestamp;
				break;
			case ZaloEventType::SENDSTICKERMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $stickerid . $href . $timestamp;
				break;
			case ZaloEventType::SENDGIFMSG:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $msgid . $href . $thumb . $timestamp;
				break;
			case ZaloEventType::FOLLOW:
			case ZaloEventType::UNFOLLOW:
				$data = (empty($appid) ? $zalo_config->oa_id : $zalo_config->app_id) . $fromuid . $timestamp;
				break;
			case ZaloEventType::MSG_DELIVERED:
				$data = $oaid . $fromuid . $msgid . $timestamp;
				break;
			case  ZaloEventType::OS_SEND_MSG:
				$data = $oaid . $fromuid . $msgid . $msginfo . $timestamp;
				break;
			default:
				$data = '';
		}
		$data .= $zalo_config->oa_secret;
		
		$hash = hash('sha256',$data);
		Yii::debug('request mac --> ' . $mac);
		Yii::debug('check mac --> ' . $hash);
		
		return $hash == $mac;
	}
	
	public static function ValidateMacV2(){
		$signature    = Yii::$app->request->headers->get('x-zevent-signature');
		$signatureArr = explode('=',$signature,2);
		$mac          = end($signatureArr);
		$data         = Yii::$app->request->rawBody;
		$jsonData     = json_decode($data,true);
		$timestamp    = $jsonData['timestamp'] ?? 0;
		$zaloConfig   = new ConfigZalo();
		Yii::debug($zaloConfig->app_id . $data . $timestamp . $zaloConfig->oa_secret);
		$hash = hash('sha256',$zaloConfig->app_id . $data . $timestamp . $zaloConfig->oa_secret);
		Yii::debug('request mac --> ' . $mac);
		Yii::debug('check mac --> ' . $hash);
		
		return $hash == $mac;
	}
}