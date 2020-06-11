<?php

namespace tas\social\controllers\hook;


use Yii;
use yii\rest\Controller;
use yii\web\JsonParser;

class LiveChatController extends Controller{
	public function init(){
		parent::init();
		Yii::$app->request->parsers = [
			'application/json' => JsonParser::class,
		];
	}
	
	public function actionIndex(){
		return 'ok';
	}
}