<?php

namespace tas\social\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "social_hook_request".
 *
 * @property int    $id_social_hook_request
 * @property string $data
 * @property int    $status
 * @property int    $created_at
 */
class SocialHookRequest extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_hook_request';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'string'],
            [['status', 'created_at'], 'integer'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_social_hook_request' => Yii::t('social', 'Id Social Hook Request'),
            'data'                   => Yii::t('social', 'Data'),
            'status'                 => Yii::t('social', 'Status'),
            'created_at'             => Yii::t('social', 'Created At'),
        ];
    }
    
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }
}
