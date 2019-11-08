<?php

namespace tas\social\models;

use Yii;

/**
 * This is the model class for table "facebook_reply_comment".
 *
 * @property integer $facebook_reply_comment_id
 * @property integer $facebook_comment_id
 * @property string  $name
 * @property string  $id
 * @property string  $comment_id
 * @property string  $reply_comment_id
 * @property string  $message
 * @property string  $created_time
 */
class FacebookReplyComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facebook_reply_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['facebook_comment_id'], 'required'],
            [['facebook_comment_id'], 'integer'],
            [['message'], 'string'],
            [['created_time'], 'safe'],
            [['name', 'id', 'comment_id', 'reply_comment_id'], 'string', 'max' => 255],
            [['reply_comment_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'facebook_reply_comment_id' => Yii::t('app', 'Facebook Reply Comment ID'),
            'facebook_comment_id'       => Yii::t('app', 'Facebook Comment ID'),
            'name'                      => Yii::t('app', 'Name'),
            'id'                        => Yii::t('app', 'ID'),
            'comment_id'                => Yii::t('app', 'Comment ID'),
            'reply_comment_id'          => Yii::t('app', 'Reply Comment ID'),
            'message'                   => Yii::t('app', 'Message'),
            'created_time'              => Yii::t('app', 'Created Time'),
        ];
    }
}
