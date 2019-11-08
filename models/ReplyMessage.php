<?php
/**
 *
 * User: ThangDang
 * Date: 12/13/17
 * Time: 11:08 PM
 *
 */

namespace tas\social\models;


use tas\social\models\config\ConfigFacebook;
use tas\social\models\config\ConfigZalo;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class ReplyMessage extends Model
{
    public $receiver_id;
    public $sender_id;
    public $message;
    public $conversations_id;
    public $type;
    
    public function rules()
    {
        return [
            [['receiver_id', 'message', 'conversations_id', 'type'], 'required'],
            [['receiver_id', 'message'], 'string'],
        ];
    }
    
    public function sendMsg()
    {
        $msg_id = false;
        switch ($this->type) {
            case Conversation::TYPE_FACEBOOK:
                $msg_id = $this->sendFB();
                break;
            case  Conversation::TYPE_ZALO:
                $msg_id = $this->sendZalo($this->receiver_id, $this->message);
                break;
            case Conversation::TYPE_VIBER:
                $msg_id = $this->sendViver($this->receiver_id, $this->message);
                break;
        }
        if ($msg_id) {
            $msg                  = new ConversationDetail();
            $msg->conversation_id = $this->conversations_id;
            $msg->msg_id          = $msg_id;
            $msg->content         = $this->message;
            $msg->created_time    = (string)time();
            $msg->sender_id       = $this->sender_id;
            $msg->user_id         = Yii::$app->user ? Yii::$app->user->id : 0;
            if (!$msg->save()) {
                \Yii::debug($msg->errors);
            }
            
            $conversation             = Conversation::findOne(['conversation_id' => $this->conversations_id]);
            $conversation->updated_at = new Expression('NOW()');
            $conversation->save();
            
            return $msg;
        }
        
        $this->addError('message', 'send msg error');
        return null;
    }
    
    private function sendFB()
    {
        $facebook_config = new ConfigFacebook();
        try {
            
            $data = [
                'recipient' => [
                    'id' => $this->receiver_id,
                ],
                'message'   => [
                    'text' => $this->message,
                ],
            ];
            
            $fb = new Facebook([
                'app_id'     => $facebook_config->app_id,
                'app_secret' => $facebook_config->app_secret,
            ]);
            
            if (empty($facebook_config->page_token)) {
                Yii::error('empty page token');
                return null;
            }
            
            try {
                /** @var \Facebook\FacebookResponse $response */
                $response = $fb->post(
                    '/me/messages',
                    $data,
                    $facebook_config->page_token
                );
                Yii::debug($response->getBody());
                $data = json_decode($response->getBody());
                if ($data && isset($data->message_id)) {
                    return $data->message_id;
                }
                
                Yii::error('Parse data error');
                return null;
            } catch (FacebookSDKException $e) {
                Yii::error($e);
                return null;
            }
        } catch (FacebookSDKException $e) {
            Yii::error($e);
            return null;
        }
    }
    
    private function sendZalo($receiver, $msg, $type = 'text')
    {
        $zalo_config = new ConfigZalo();
        
        $oaid      = $zalo_config->oa_id;
        $data      = json_encode([
            'uid'     => $receiver,
            'message' => $msg,
        ]);
        $timestamp = round(microtime(true) * 1000);
        $mac       = hash('sha256', $oaid . $data . $timestamp . $zalo_config->oa_secret);
        
        $params = [
            'oaid'      => $oaid,
            'data'      => $data,
            'timestamp' => $timestamp,
            'mac'       => $mac,
        ];
        \Yii::trace($params);
        
        $json_data = json_encode($data);
        $ch        = curl_init('https://openapi.zaloapp.com/oa/v1/sendmessage/text');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        $response = json_decode($response);
        \Yii::trace($response);
        
        return isset($response->data) ? $response->data->msgId : false;
    }
    
    private function sendViver($receiver, $msg, $type = 'text')
    {
        
        $data = [
            'receiver'        => $receiver,
            'min_api_version' => 1,
            'sender'          => [
                'name'   => \Yii::$app->params['viber']['name'],
                'avatar' => \Yii::$app->params['viber']['avatar'],
            ],
            'type'            => $type,
            'text'            => $msg,
        ];
        
        $json_data = json_encode($data);
        $ch        = curl_init("https://chatapi.viber.com/pa/send_message");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data),
                'X-Viber-Auth-Token :' . \Yii::$app->params['viber']['token'],
            ]
        );
        $response = curl_exec($ch);
        $response = json_decode($response);
        
        return (string)$response->message_token;
    }
}