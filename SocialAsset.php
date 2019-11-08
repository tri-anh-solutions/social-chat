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

class SocialAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->sourcePath                  = __DIR__ . '/assets';
        $this->publishOptions['forceCopy'] = true;
    }
    
    public $css = [
        'css/custom.css',
    ];
    
    public $js = [
        'js/functions.js',
        'js/chat.js',
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}