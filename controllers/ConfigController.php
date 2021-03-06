<?php
/**
 *
 * User: ThangDang
 * Date: 7/24/18
 * Time: 10:16
 *
 */

namespace tas\social\controllers;


use Exception;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use tas\social\components\FacebookHelper;
use tas\social\models\config\ConfigFacebook;
use tas\social\models\config\ConfigLHC;
use tas\social\models\config\ConfigZalo;
use tas\social\models\config\ModuleConfig;
use tas\social\models\forms\ViberConfigForm;
use tas\social\models\forms\ViberRegisterWebHookForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use function json_decode;

class ConfigController extends Controller{
	/**
	 * @inheritdoc
	 */
	public function behaviors(){
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only'  => ['index'],
				'rules' => [
					[
						'actions' => ['index'],
						'allow'   => true,
						'roles'   => ['@'],
					],
				],
			],
		];
	}
	
	/**
	 * @return string
	 * @throws \Facebook\Exceptions\FacebookSDKException
	 */
	public function actionIndex($active = 'base'){
		$moduleConfig   = new ModuleConfig();
		$zaloConfig     = new ConfigZalo();
		$facebookConfig = new ConfigFacebook();
		$lhcConfig      = new ConfigLHC();
		$viberConfig    = new ViberConfigForm();
		
		//if($zaloConfig->load(Yii::$app->request->post())
		//   && $zaloConfig->validate()
		//   && $facebookConfig->load(Yii::$app->request->post())
		//   && $facebookConfig->validate()
		//   && $lhcConfig->load(Yii::$app->request->post())
		//   && $lhcConfig->validate()
		//   && $moduleConfig->load(Yii::$app->request->post())
		//   && $moduleConfig->validate()
		//){
		//	$zaloConfig->update();
		//	$facebookConfig->update();
		//	$lhcConfig->update();
		//	$moduleConfig->update();
		//	Yii::$app->session->addFlash('success',Yii::t('social','Update config success'));
		//}
		
		$loginUrl = '';
		if(!empty($facebookConfig->app_id)){
			$fb                 = new Facebook([
				'app_id'     => $facebookConfig->app_id,
				'app_secret' => $facebookConfig->app_secret,
			]);
			$helper             = $fb->getRedirectLoginHelper();
			$permissions        = ['manage_pages','pages_messaging']; // optional
			$facebook_login_url = Yii::$app->urlManager->createAbsoluteUrl([$this->module->id . '/config/facebook-login']);
			$loginUrl           = Html::a('Authorized',$helper->getLoginUrl($facebook_login_url,$permissions));
		}
		
		$fb_logged = false;
		/** @var \Facebook\Authentication\AccessToken $fb_access_token */
		if(!empty($facebookConfig->access_token)){
			if(!FacebookHelper::checkTokenValid()){
				// Yii::$app->session->remove('facebook_access_token');
				$facebookConfig->access_token            = '';
				$facebookConfig->access_token_expires_at = 0;
				
				$fb_logged = false;
			}else{
				$fb_logged = true;
			}
		}
		
		return $this->render('index',[
			'zalo_config'        => $zaloConfig,
			'zalo_hook'          => Yii::$app->urlManager->createAbsoluteUrl([$this->module->id . '/hook/zalo']),
			'lhc_hook'           => Yii::$app->urlManager->createAbsoluteUrl([$this->module->id . '/hook/live-chat']),
			'viber_hook'         => Yii::$app->urlManager->createAbsoluteUrl([$this->module->id . '/hook/viber']),
			'facebook_config'    => $facebookConfig,
			'facebook_hook'      => Yii::$app->urlManager->createAbsoluteUrl([$this->module->id . '/hook/facebook']),
			'facebook_login_url' => $loginUrl,
			'fb_logged'          => $fb_logged,
			'moduleConfig'       => $moduleConfig,
			'lhc_config'         => $lhcConfig,
			'viber_config'       => $viberConfig,
			'active'             => $active,
		]);
	}
	
	public function actionUpdateConfig($active = 'base'){
		$moduleConfig   = new ModuleConfig();
		$zaloConfig     = new ConfigZalo();
		$facebookConfig = new ConfigFacebook();
		$lhcConfig      = new ConfigLHC();
		$viberConfig    = new ViberConfigForm();
		
		if($zaloConfig->load(Yii::$app->request->post()) && $zaloConfig->validate()){
			$zaloConfig->update();
		}
		
		if($facebookConfig->load(Yii::$app->request->post()) && $facebookConfig->validate()){
			$facebookConfig->update();
		}
		
		if($lhcConfig->load(Yii::$app->request->post()) && $lhcConfig->validate()){
			$lhcConfig->update();
		}
		
		if($moduleConfig->load(Yii::$app->request->post()) && $moduleConfig->validate()){
			$moduleConfig->update();
		}
		if($viberConfig->load(Yii::$app->request->post()) && $viberConfig->validate()){
			$viberConfig->update();
		}
		
		Yii::$app->session->addFlash('success',Yii::t('social','Update config success'));
		
		return $this->redirect(['config/index','active' => $active]);
	}
	
	public function actionViberRegisterHook(){
		$registerWebHookModel = new ViberRegisterWebHookForm();
		$registerWebHookModel->load(Yii::$app->request->post());
		$registerWebHookModel->save();
		
		return $this->redirect(['config/index','active' => 'viber']);
	}
	
	public function actionGetPages(){
		Yii::$app->response->format = Response::FORMAT_JSON;
		$result                     = [
			'success' => false,
			'data'    => null,
			'error'   => null,
		];
		
		try{
			$data              = FacebookHelper::getPages();
			$result['success'] = true;
			$result['data']    = $data;
			
			return $result;
		}
		catch(Exception $e){
			Yii::error($e);
			$result['error'] = $e->getMessage();
			
			return $result;
		}
	}
	
	public function actionPageAccessToken($page_id = null){
		$facebook_config = new ConfigFacebook();
		try{
			$fb = new Facebook([
				'app_id'     => $facebook_config->app_id,
				'app_secret' => $facebook_config->app_secret,
			]);
			
			$fb_access_token = new AccessToken($facebook_config->access_token,$facebook_config->access_token_expires_at);
			try{
				if(!$fb_access_token->getValue()){
					return '';
				}
				// Returns a `Facebook\FacebookResponse` object
				/** @var \Facebook\FacebookResponse $response */
				$response = $fb->get(
					'/' . ($page_id ?: $facebook_config->page_id) . '?fields=access_token',
					$fb_access_token->getValue()
				);
				Yii::debug($response->getBody());
				$data = json_decode($response->getBody(),false);
				Yii::$app->session->set('page_access_token',$data->access_token);
				$facebook_config->page_token = $data->access_token;
				$facebook_config->update();
				
				$response = $fb->get(
					'/oauth/access_token?' . http_build_query([
						'grant_type'        => 'fb_exchange_token',
						'client_id'         => $facebook_config->app_id,
						'client_secret'     => $facebook_config->app_secret,
						'fb_exchange_token' => $facebook_config->page_token,
					]),
					$fb_access_token->getValue()
				);
				try{
					$data = json_decode($response->getBody(),false);
					Yii::$app->session->set('page_long_access_token',$data->access_token);
					$facebook_config->long_page_token = $data->access_token;
					$facebook_config->update();
				}
				catch(Exception $ex){
					Yii::error($ex);
				}
				
				return $data->access_token;
			}
			catch(FacebookSDKException $e){
				Yii::error('Facebook SDK returned an error: ' . $e->getMessage());
				
				return '';
			}
		}
		catch(FacebookSDKException $e){
			Yii::error($e);
			
			return '';
		}
	}
	
	/**
	 * @param string $action
	 *
	 * @return array
	 */
	public function actionSubscribedApps($action = 'get',$page_id = null){
		$success = false;
		$error   = null;
		$data    = null;
		
		$facebook_config            = new ConfigFacebook();
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		try{
			$fb = new Facebook([
				'app_id'     => $facebook_config->app_id,
				'app_secret' => $facebook_config->app_secret,
			]);
			
			$pages      = FacebookHelper::getPages();
			$page_token = '';
			$page_id    = $page_id ?: $facebook_config->page_id;
			if(count($pages)){
				foreach($pages as $page){
					if($page_id == $page->id){
						$page_token = $page->access_token;
					}
				}
			}
			
			Yii::debug('PAGE ID: ' . $page_id);
			Yii::debug('PAGE TOKEN ' . $page_token);
			
			try{
				if($action == 'delete'){
					$response = $fb->delete(
						'/' . $page_id . '/subscribed_apps',
						[],
						$page_token
					);
					$success  = json_decode($response->getBody())->success;
				}elseif($action == 'create'){
					$response = $fb->post(
						'/' . $page_id . '/subscribed_apps',
						[],
						$page_token
					);
					$success  = json_decode($response->getBody())->success;
				}else{
					$response = $fb->get(
						'/' . $page_id . '/subscribed_apps',
						$page_token
					);
					$success  = true;
					$data     = json_decode($response->getBody())->data;
				}
			}
			catch(FacebookSDKException $e){
				$error = 'Facebook SDK returned an error: ' . $e->getMessage();
				Yii::debug('page id ' . $facebook_config->page_id);
				$success = false;
			}
		}
		catch(FacebookSDKException $e){
			Yii::error($e);
			$success = false;
			$error   = 'Facebook SDK returned an error: ' . $e->getMessage();
		}
		
		return [
			'success' => $success,
			'data'    => $data,
			'error'   => $error,
		];
	}
	
	/**
	 * @return \yii\web\Response
	 */
	public function actionFacebookLogin(){
		$facebook_config = new ConfigFacebook();
		try{
			$fb     = new Facebook([
				'app_id'     => $facebook_config->app_id,
				'app_secret' => $facebook_config->app_secret,
			]);
			$helper = $fb->getRedirectLoginHelper();
			/** @var $accessToken \Facebook\Authentication\AccessToken|null */
			try{
				$accessToken = $helper->getAccessToken();
			}
			catch(FacebookResponseException $e){
				// When Graph returns an error
				echo 'Graph returned an error: ' . $e->getMessage();
				Yii::error($e);
				exit;
			}
			catch(FacebookSDKException $e){
				Yii::error($e);
				// When validation fails or other local issues
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}
			
			if($accessToken !== null){
				// Logged in!
				// Yii::$app->session->set('facebook_access_token', $accessToken);
				Yii::debug($accessToken);
				$facebook_config->access_token            = $accessToken->getValue();
				$facebook_config->access_token_expires_at = $accessToken->getExpiresAt() != null ? $accessToken->getExpiresAt()->getTimestamp() : 0;
				$facebook_config->update();
				
				return $this->redirect(['config/index']);
				// Now you can redirect to another page and use the
				// access token from $_SESSION['facebook_access_token']
			}elseif($helper->getError()){
				// The user denied the request
				Yii::error($helper->getError());
				exit;
			}
			
		}
		catch(FacebookSDKException $e){
			Yii::error($e);
		}
	}
	
	/**
	 * @return \yii\web\Response
	 */
	public function actionFacebookLogout(){
		$facebook_config                          = new ConfigFacebook();
		$facebook_config->access_token            = '';
		$facebook_config->access_token_expires_at = 0;
		$facebook_config->update();
		
		// Yii::$app->session->remove('facebook_access_token');
		return $this->redirect(['config/index']);
	}
}