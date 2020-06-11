<?php

namespace tas\social;

use Yii;

/**
 * social module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'tas\social\controllers';
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if (!isset(Yii::$app->i18n->translations['social'])) {
            Yii::$app->i18n->translations['social'] = [
	            'class'          => 'yii\i18n\PhpMessageSource',
	            'sourceLanguage' => 'en',
	            'basePath'       => __DIR__ . '/messages',
            ];
        }
    }
}
