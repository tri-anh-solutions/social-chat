<?php

namespace tas\social;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * social module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'tas\social\controllers';
    public $createTicketUrl = '/feedback/create?id_customer={{id_customer}}&id_source={{source}}&conversation_detail_ids={{msg_ids}}';
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        
        if (!isset(Yii::$app->i18n->translations['social'])) {
            Yii::$app->i18n->translations['social*'] = [
	            'class'          => PhpMessageSource::class,
	            'sourceLanguage' => 'en',
	            'basePath'       => __DIR__ . '/messages',
            ];
        }
    }
}
