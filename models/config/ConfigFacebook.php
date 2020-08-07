<?php
/**
 * Created by PhpStorm.
 * User: T�m
 * Date: 7/23/2015
 * Time: 8:32 PM
 */

namespace tas\social\models\config;

class ConfigFacebook extends ConfigModel
{
    public $verify_token;
    public $app_id;
    public $app_secret;
    public $page_id;
    public $page_token;
    public $page_token_expire;
    
    public $access_token = '';
    public $access_token_expires_at = 0;
    
    public function type()
    {
        return 'facebook';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['verify_token', 'page_token', 'app_id', 'app_secret', 'page_id'], 'filter', 'filter' => 'trim'],
            [['verify_token', 'app_id', 'app_secret'], 'required'],
        ];
    }
}