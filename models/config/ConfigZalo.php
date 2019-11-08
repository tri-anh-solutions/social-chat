<?php
/**
 * Created by PhpStorm.
 * User: T�m
 * Date: 7/23/2015
 * Time: 8:32 PM
 */

namespace tas\social\models\config;

class ConfigZalo extends ConfigModel
{
    public $oa_id;
    public $oa_secret;
    public $app_id;
    public $app_secret;
    
    public function type()
    {
        return 'zalo';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oa_id', 'oa_secret', 'app_id', 'app_secret'], 'filter', 'filter' => 'trim'],
            [['oa_id', 'oa_secret', 'app_id', 'app_secret'], 'required'],
        ];
    }
}