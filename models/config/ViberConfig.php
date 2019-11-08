<?php
/**
 *
 * User: ThangDang
 * Date: 8/8/18
 * Time: 17:52
 *
 */

namespace tas\social\models\config;


class ViberConfig extends ConfigModel
{
    public $name;
    public $avatar;
    
    public function getType()
    {
        return 'viber';
    }
}