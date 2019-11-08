<?php
/**
 *
 * User: ThangDang
 * Date: 8/7/18
 * Time: 18:54
 *
 */

namespace tas\social\components;


use tas\social\models\config\ConfigFacebook;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Yii;

class FacebookHelper
{
    /**
     * @return array
     */
    public static function getPages()
    {
        $facebook_config = new ConfigFacebook();
        try {
            $fb = new Facebook([
                'app_id'     => $facebook_config->app_id,
                'app_secret' => $facebook_config->app_secret,
            ]);
            if (empty($facebook_config->access_token)) {
                return [];
            }
            
            $fb_access_token = new AccessToken($facebook_config->access_token, $facebook_config->access_token_expires_at);
            
            if ($fb_access_token->isExpired()) {
                return [];
            }
            try {
                /** @var \Facebook\FacebookResponse $response */
                $response = $fb->get(
                    '/me/accounts',
                    $fb_access_token->getValue()
                );
                // Yii::debug($response->getBody());
                $data = json_decode($response->getBody());
                if ($data && isset($data->data)) {
                    return $data->data;
                }
                
                Yii::error('Parse data error');
                return [];
            } catch (FacebookSDKException $e) {
                Yii::error($e);
                return [];
            }
        } catch (FacebookSDKException $e) {
            Yii::error($e);
            return [];
        }
    }
    
    /**
     * @return bool
     */
    public static function checkTokenValid()
    {
        $facebook_config = new ConfigFacebook();
        try {
            if (empty($facebook_config->access_token)) {
                return false;
            }
            
            $fb_access_token = new AccessToken($facebook_config->access_token, $facebook_config->access_token_expires_at);
            if ($fb_access_token->isExpired()) {
                return false;
            }
            $fb       = new Facebook([
                'app_id'     => $facebook_config->app_id,
                'app_secret' => $facebook_config->app_secret,
            ]);
            $response = $fb->get('/me?fields=id,name',
                $fb_access_token->getValue());
            if (json_decode($response->getBody())) {
                return true;
            }
            
        } catch (FacebookSDKException $e) {
            Yii::error($e);
            return false;
        }
        
    }
}