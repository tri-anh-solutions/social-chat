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
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\httpclient\Client;
use yii\web\UploadedFile;
use function explode;
use function implode;
use function json_decode;

class ViberRegisterWebHookForm extends Model{
	/** @var UploadedFile */
	public $eventTypes;
	public $sendName  = false;
	public $sendPhoto = false;
	public $hookUrl;
	
	/** @var ViberConfig */
	private $_config;
	
	public function init(){
		parent::init();
		$this->_config    = new ViberConfig();
		$this->eventTypes = explode(',',$this->_config->eventTypes);
		$this->sendName   = $this->_config->sendName;
		$this->sendPhoto  = $this->_config->sendPhoto;
	}
	
	public function rules(){
		return [
			['eventTypes','safe'],
			[['sendName','sendPhoto'],'boolean','trueValue' => true],
			['hookUrl','string'],
		];
	}
	
	public function save(){
		if($this->validate()){
			$this->hookUrl             = 'https://fbhook.thangdv.com/web/social/hook/viber';
			$this->_config->eventTypes = implode(',',$this->eventTypes);
			$this->_config->sendName   = $this->sendName;
			$this->_config->sendPhoto  = $this->sendPhoto;
			$this->_config->update();
			$data   = [
				'url'         => $this->hookUrl,
				'event_types' => $this->eventTypes,
				'send_name'   => (bool)$this->sendName,
				'send_photo'  => (bool)$this->sendPhoto,
			];
			$client = new Client([
				'baseUrl' => 'https://chatapi.viber.com/pa/set_webhook',
			]);
			try{
				$request = $client->createRequest()
				                  ->setFormat(Client::FORMAT_JSON)
				                  ->setData($data)
				                  ->setHeaders([
					                  'X-Viber-Auth-Token :' . $this->_config->token,
				                  ]);
				
				/** @var \yii\httpclient\Response $response */
				$response = $request->send();
				if($response->isOk){
					Yii::debug($response->content);
					$response_data = json_decode($response->content,true);
					
					return $response_data['status'] == 0;
				}
				
				return null;
			}
			catch(InvalidConfigException $e){
				Yii::error($e);
				
				return null;
			}
		}
	}
	
	public function attributeLabels(){
		return [
			'sendPhoto'  => Yii::t('social.viber','Send Photo'),
			'sendName'   => Yii::t('social.viber','Send Name'),
			'eventTypes' => Yii::t('social.viber','Event Types'),
		];
	}
}