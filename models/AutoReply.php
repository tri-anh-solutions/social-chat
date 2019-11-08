<?php

namespace tas\social\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%social_auto_reply}}".
 *
 * @property int    $id_social_auto_reply
 * @property string $title
 * @property string $message
 * @property string $reply_content
 * @property int    $created_at
 * @property int    $updated_at
 */
class AutoReply extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%social_auto_reply}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reply_content'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['title', 'message'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_social_auto_reply' => Yii::t('app', 'Id Social Auto Reply'),
            'title'                => Yii::t('app', 'Title'),
            'message'              => Yii::t('app', 'Message'),
            'reply_content'        => Yii::t('app', 'Reply Content'),
            'created_at'           => Yii::t('app', 'Created At'),
            'updated_at'           => Yii::t('app', 'Updated At'),
        ];
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
