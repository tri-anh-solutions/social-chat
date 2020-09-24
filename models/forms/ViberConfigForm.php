<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:23
 *
 */

namespace tas\social\models\forms;


use tas\social\models\config\ViberConfig;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use function file_exists;
use function ini_get;
use function is_dir;
use function mkdir;
use function time;

class ViberConfigForm extends Model{
	/** @var UploadedFile */
	public $upload;
	public $name;
	public $token;
	public $avatar;
	/** @var ViberConfig */
	private $_config;
	
	public function init(){
		parent::init();
		$this->_config = new ViberConfig();
		$this->name    = $this->_config->name;
		$this->token   = $this->_config->token;
		$this->avatar  = $this->_config->avatar;
	}
	
	public function rules(){
		return [
			[['name','token'],'safe'],
			[['name','token'],'required'],
			[
				'upload',
				'image',
				//'skipOnEmpty' => true,
				'extensions' => ['jpg','jpeg','png','gif'],
				'mimeTypes'  => ['image/jpeg','image/pjpeg','image/png','image/gif'],
				'maxSize'    => ((int)ini_get('upload_max_filesize') ?: 2) * 1024 * 1024,
			],
		];
	}
	
	public function update(){
		$this->upload = UploadedFile::getInstance($this,'upload');
		if($this->validate()){
			if($this->upload){
				$savePath = Yii::getAlias('@webroot') . '/uploads/social';
				$saveFile = $this->upload->baseName . '_' . time() . '.' . $this->upload->extension;
				if(!file_exists($savePath) && !mkdir($savePath,0777,true) && !is_dir($savePath)){
					throw new \RuntimeException(sprintf('Directory "%s" was not created',$savePath));
				}
				$this->upload->saveAs($savePath . '/' . $saveFile);
				
				$this->_config->avatar = Yii::$app->urlManager->createAbsoluteUrl('/uploads/social/' . $saveFile);
			}
			$this->_config->name  = $this->name;
			$this->_config->token = $this->token;
			
			return $this->_config->update();
		}
		
		return false;
	}
	
	public function attributeLabels(){
		return [
			'upload' => Yii::t('social.viber','Avatar'),
			'name'   => Yii::t('social.viber','Sender Name'),
			'token'  => Yii::t('social.viber','Token'),
		];
	}
	
	
}