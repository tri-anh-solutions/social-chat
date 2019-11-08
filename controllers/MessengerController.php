<?php
/**
 *
 * User: ThangDang
 * Date: 4/10/18
 * Time: 23:18
 *
 */

namespace tas\social\controllers;


use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use tas\social\models\ReplyMessage;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;
use const SORT_DESC;

/** @noinspection LongInheritanceChainInspection */

class MessengerController extends Controller
{
    /**
     * Lists all Conversation models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionGetConversation($page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $per_page                   = 5;
        
        $query = Conversation::find()->orderBy(['updated_at' => SORT_DESC]);
        
        $provider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'defaultPageSize' => $per_page,
                'page'            => $page - 1,
                'validatePage'    => false,
            ],
        ]);
        
        return [
            'data'       => $provider->getModels(),
            'total'      => $provider->getPagination()->totalCount,
            'total_page' => $provider->getPagination()->pageCount,
            'page'       => $provider->getPagination()->getPage() + 1,
        ];
    }
    
    public function actionGetConversationDetail($id, $page = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $per_page                   = 10;
        Conversation::updateAll(['unread_count' => 0,], ['conversation_id' => $id]);
        
        $query = ConversationDetail::find()
            ->where([
                'conversation_id' => $id,
            ]);
        
        $provider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => [
                'defaultOrder' => [
                    'conversation_detail_id' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => $per_page,
                'page'            => $page - 1,
                'validatePage'    => false,
            ],
        ]);
        
        return [
            'data'       => $page == 1 ? array_reverse($provider->getModels()) : $provider->getModels(),
            'total'      => $provider->getPagination()->totalCount,
            'total_page' => $provider->getPagination()->pageCount,
            'page'       => $provider->getPagination()->getPage() + 1,
        ];
    }
    
    public function actionSendMsg($id, $msg = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $conversation               = Conversation::findOne($id);
        if ($conversation == null) {
            return [
                'success' => false,
                'error'   => Yii::t('social', 'Can`\'t find conversation'),
                'data'    => null,
            ];
        }
        
        $conversation->unread_count = 0;
        $conversation->save();
        
        $reply_form = new ReplyMessage();
        
        $reply_form->receiver_id      = $conversation->sender_id;
        $reply_form->sender_id        = $conversation->receiver_id;
        $reply_form->type             = $conversation->type;
        $reply_form->conversations_id = $id;
        $reply_form->message          = $msg;
        
        
        $status = false;
        $msg    = null;
        $error  = false;
        if ($reply_form->validate() && ($msg = $reply_form->sendMsg())) {
            $status = true;
        } else {
            $error = $msg ? $msg->getErrors() : Yii::t('social', 'Send message error');
        }
        
        return [
            'success' => $status,
            'error'   => $error,
            'data'    => $msg,
        ];
    }
}