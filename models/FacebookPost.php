<?php


/**
 *
 */

namespace tas\social\models;

use app\components\HashId;

/**
 * This is the model class for table "facebook_post".
 *
 * @property integer           $facebook_post_id
 * @property string            $message
 * @property string            $from_name
 * @property string            $from_id
 * @property string            $created_time
 * @property string            $updated_time
 * @property string            $post_id
 *
 * @property FacebookComment[] $comments
 * @property string            $encryptId
 */
class FacebookPost extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'facebook_post';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['created_time', 'updated_time'], 'safe'],
            [['from_name', 'from_id', 'post_id'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'facebook_post_id' => 'Facebook Post ID',
            'message'          => 'Message',
            'from_name'        => 'From Name',
            'from_id'          => 'From ID',
            'created_time'     => 'Created Time',
            'updated_time'     => 'Updated Time',
            'post_id'          => 'Post ID',
        ];
    }
    
    public function getComments()
    {
        return $this->hasMany(FacebookComment::className(), ['post_id' => 'post_id', 'parent_id' => 'post_id']);
    }
    
    public function getEncryptId()
    {
        return HashId::encode($this->facebook_post_id);
    }
}
