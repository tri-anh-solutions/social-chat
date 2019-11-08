<?php

namespace tas\social\models;

/**
 * This is the model class for table "facebook_comment".
 *
 * @property integer                                    $facebook_comment_id
 * @property string                                     $comment_id
 * @property string                                     $post_id
 * @property string                                     $parent_id
 * @property string                                     $id
 * @property string                                     $name
 * @property string                                     $message
 * @property string                                     $created_time
 *
 * @property \tas\social\models\FacebookComment $subComments
 */
class FacebookComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facebook_comment';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'post_id'], 'required'],
            [['message'], 'string'],
            [['created_time'], 'safe'],
            [['comment_id', 'post_id', 'parent_id', 'id', 'name'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'facebook_comment_id' => 'Facebook Comment ID',
            'comment_id'          => 'Comment ID',
            'post_id'             => 'Post ID',
            'parent_id'           => 'Parent ID',
            'id'                  => 'ID',
            'name'                => 'Name',
            'message'             => 'Message',
            'created_time'        => 'Created Time',
        ];
    }
    
    public function getSubComments()
    {
        return $this->hasMany(self::className(), ['parent_id' => 'comment_id']);
    }
}
