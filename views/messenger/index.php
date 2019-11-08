<?php

/**
 *
 * User: ThangDang
 * Date: 4/10/18
 * Time: 23:26
 *
 */

use tas\social\models\Conversation;
use tas\social\models\ConversationDetail;
use tas\social\SocialAsset;
use yii\helpers\Url;
use yii\web\View;

/* @var $this \yii\web\View */

$this->title                   = Yii::t('social', 'Conversations');
$this->params['breadcrumbs'][] = $this->title;
SocialAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@app/modules/social/assets');
?>
    <div class="conversation-index">
        <div class="container-fluid main-section">
            <div class="row">
                <div class="col-md-3 col-sm-3 col-xs-12 left-sidebar">
                    <div class="input-group searchbox">
                        <input class="form-control" placeholder="Search" name="srch-term" id="srch-term" type="text">
                        <div class="input-group-btn">
                            <button class="btn btn-default search-icon" type="submit"><i
                                        class="glyphicon glyphicon-search"></i></button>
                        </div>
                    </div>
                    <div class="left-chat">
                        <ul></ul>
                    </div>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12 right-sidebar">
                    <div class="row">
                        <div class="col-md-12 right-header">
                            <div class="right-header-img">
                                <img src="<?= $directoryAsset; ?>/images/businessman.png">
                            </div>
                            <div class="right-header-detail">
                                <p></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 right-header-contentChat">
                            <div class="load-more-text text-center" style="display: none;"><a id="load-more-msg" href="#"><?= Yii::t('social',
                                        'load old message'); ?></a></div>
                            <div class="load-more-loading text-center" style="display: none;"><i
                                        class="fa fa-refresh fa-spin"></i> <?= Yii::t('social', 'loading ...'); ?>
                            </div>
                            <ul>

                            </ul>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 right-chat-textbox">
                            <input id="msg-content" type="text"><a id="btn-send-msg" href="#"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div
    </div>
    <div id="user-template" style="display: none;">
        <li>
            <div class="chat-left-img">
                <img src="">
            </div>
            <div class="chat-left-detail">
                <p></p>
                <span class="chat-left-updated-at"></span>
            </div>
            <span class="badge chat-left-unread-count"></span>
        </li>
    </div>
    <div id="msg-template" style="display: none;">
        <li>
            <div class="">
                <span><span class="name"></span> <small></small> </span><br><br>
                <p>
                </p>
            </div>
        </li>
    </div>
<?php
$conversation_url        = Url::to(['messenger/get-conversation']);
$send_msg_url            = Url::to(['messenger/send-msg']);
$conversation_detail_url = Url::to(['messenger/get-conversation-detail']);
$zalo_logo               = "$directoryAsset/images/zalo.jpg";
$viber_logo              = "$directoryAsset/images/viber.jpg";
$facebook_logo           = "$directoryAsset/images/facebook.jpg";
$type_fb                 = Conversation::TYPE_FACEBOOK;
$type_viber              = Conversation::TYPE_VIBER;
$type_zalo               = Conversation::TYPE_ZALO;

$msg_type_text = ConversationDetail::TYPE_TEXT;
$msg_type_img  = ConversationDetail::TYPE_IMG;
$msg_type_link = ConversationDetail::TYPE_LINK;
$js            = <<<JS
var CONVERSATION_URL = '{$conversation_url}';
var CONVERSATION_DETAIL_URL = '{$conversation_detail_url}';
var SEND_MSG_URL = '{$send_msg_url}';
var ZALO_LOGO = '{$zalo_logo}';
var FACEBOOK_LOGO = '{$facebook_logo}';
var VIBER_LOGO = '{$viber_logo}';
var TYPE_FB = {$type_fb};
var TYPE_VIBER = {$type_viber};
var TYPE_ZALO = {$type_zalo};
var MSG_TYPE_TEXT = '{$msg_type_text}';
var MSG_TYPE_IMG = '{$msg_type_img}';
var MSG_TYPE_LINK = '{$msg_type_link}';
var user_template = $('#user-template li:first');
var msg_template = $('#msg-template li:first');

var CURRENT_USER_ID = 0;
var CURRENT_LIST_USER_PAGE = 1;
var CURRENT_LIST_MSG_PAGE = 1;
var SENDER_ID = '';
var SENDER_NAME = '';
var LIST_USER;
JS;
$this->registerJs($js, View::POS_END);