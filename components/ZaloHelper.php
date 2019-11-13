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