<?php
/**
 *
 * User: ThangDang
 * Date: 12/19/17
 * Time: 10:25 PM
 *
 */

namespace tas\social\controllers\hook;

use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class ViberController extends Controller
{
    public function init()
    {
        parent::init();
        
        Yii::$app->request->parsers = [
            'application/json' => 'yii\web\JsonParser',
        ];
    }
    
    public function actionIndex()
    {
        \Yii::$app->response->format = Response::FORMAT_RAW;
        $data                        = \Yii::$app->request->bodyParams;
        
        if ($this->validateSign()) {
            if (isset($data['event'])) {
                if ($data['event'] == 'message') {
                    $msg = 'Reply : ' . $data['message']['text'];
                    
                    $sender_id = $data['sender']['id'];
                    /** @var Conversation $conversation */
                    $conversation = Conversation::findOne([
                        'sender_id' => $sender_id,
                        'type'      => Conversation::TYPE_VIBER,
                    ]);
                    if ($conversation == null) {
                        $conversation               = new Conversation();
                        $conversation->unread_count = 1;
                    } else {
                        $conversation->updateCounters(['unread_count' => 1]);
                    }
                    
                    $conversation->sender_id     = $sender_id;
                    $conversation->sender_name   = $data['sender']['name'];
                    $conversation->receiver_id   = '';
                    $conversation->receiver_name = '';
                    $conversation->type          = Conversation::TYPE_VIBER;
                    
                    if ($conversation->save()) {
                        $msg                  = new ConversationDetail();
                        $msg->conversation_id = $conversation->conversation_id;
                        $msg->sender_id       = $sender_id;
                        $msg->msg_id          = (string)$data['message_token'];
                        $msg->content         = $data['message']['text'];
                        $msg->created_time    = (string)$data['timestamp'];
                        $msg->type            = ConversationDetail::TYPE_TEXT;
                        if (!$msg->save()) {
                            \Yii::trace($msg->errors);
                        }
                    } else {
                        \Yii::trace($conversation->errors);
                    }
                    return 'OK';
                    //$this->sendReceiverMsg($data['sender']['id'],$msg);
                }
            }
            
        } else {
            return "Invalid Sign";
        }
    }
    
    private function sendReceiverMsg($receiver, $msg, $type = 'text')
    {
        
        $data = [
            'receiver'        => $receiver,
            'min_api_version' => 1,
            'sender'          => [
                'name'   => 'ThangDV BOT',
                'avatar' => 'https://en.gravatar.com/userimage/6214501/bf9a8e9694b1a07598282ddf30d31b75?size=200',
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
                'X-Viber-Auth-Token :' . Yii::$app->params['viber']['token'],
            ]
        );
        $response = curl_exec($ch);
        \Yii::trace($response);
        
        return $response;
    }
    
    private function validateSign()
    {
        $sign = \Yii::$app->request->get('sig');
        $raw  = \Yii::$app->request->rawBody;
        \Yii::trace($sign);
        if (empty($sign)) {
            \Yii::$app->response->statusCode = 400;
            
            return false;
        }
        
        if (hash_hmac('sha256', $raw, Yii::$app->params['viber']['token']) !== $sign) {
            \Yii::$app->response->statusCode = 400;
            
            return false;
        }
        
        return true;
    }
}