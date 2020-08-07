<?php
/**
 *
 * User: ThangDang
 * Date: 6/4/18
 * Time: 21:07
 *
 */

namespace tas\social;


use yii\web\AssetBundle;
use const YII_DEBUG;

class SocialAsset extends AssetBundle{
	/**
	 * @inheritdoc
	 */
	public function init(){
		parent::init();
		$this->sourcePath                  = __DIR__ . '/assets';
		$this->publishOptions['forceCopy'] = true;
	}
	
	public $css = [
		// 'css/chat.css',
		'css/custom.css',
	];
	
	public $js      = [
		// 'js/app-bundle.js',
		YII_DEBUG ? 'js/functions.js' : 'js/functions.min.js',
		YII_DEBUG ? 'js/chat.js' : 'js/chat.min.js',
	];
	public $depends = [
		'app\assets\AppAsset',
	];
}