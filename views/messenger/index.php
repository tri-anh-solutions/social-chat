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
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\web\View;

/* @var $this \yii\web\View */

$this->title                   = Yii::t('social','Conversations');
$this->params['breadcrumbs'][] = $this->title;
SocialAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@tas/social/assets');

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
                                <img src="<?=$directoryAsset?>/images/businessman.png">
                            </div>
                            <div class="right-header-detail">
                                <p></p>
                                <div class="right-header-detail-control">
                                    <a href="javascript:void(0)" id="chat-send-ticket" class="hidden"><?=Yii::t('social','Send ticket')?></a>
                                    <a href="javascript:void(0)" id="chat-select"><?=Yii::t('social','Select')?></a>
                                    <a href="javascript:void(0)" id="chat-cancel" class="hidden text-danger"><?=Yii::t('social','Cancel')?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 right-header-contentChat">
                            <div class="load-more-text text-center" style="display: none;"><a id="load-more-msg" href="#"><?=Yii::t('social',
										'load old message');?></a></div>
                            <div class="load-more-loading text-center" style="display: none;"><i
                                        class="fa fa-refresh fa-spin"></i> <?=Yii::t('social','loading ...');?>
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
				<?php if(class_exists('app\models\CustomerInfo')): ?>
                    <span class="chat-left-customer"><span class="chat-left-customer-id"></span><a href="javascript:void(0)"
                                                                                                   class="chat-left-set-customer-id"><?=Yii::t
							('app','Set customer')
							?></a></span>
				<?php endif; ?>
            </div>
            <span class="badge chat-left-unread-count"></span>

        </li>
    </div>
    <div id="msg-template" style="display: none;">
        <li>
            <div class="">
                <div class="chat-item">
                    <span><span class="name"></span> <small></small> </span><br><br>
                    <p>
                    </p>
                </div>
                <div class="msg-checkbox-container">
                    <label>
                        <input type="checkbox" class="msg-checkbox"/>
                    </label>
                </div>
            </div>
        </li>
    </div>
<?php Modal::begin([
	'id'      => 'messenger-modal',
	'size'    => Modal::SIZE_LARGE,
	'options' => [
		'style' => ['z-index' => 99999],
	],
	'header'  => '<h4 id="messenger-modal-title"></h4>',
]) ?>
    <div id="messenger-modal-content">

    </div>
<?php Modal::end() ?>
<?php
$search_customer_url     = Url::to(['messenger/search-customers']);
$set_customer_url        = Url::to(['messenger/set-customer']);
$conversation_url        = Url::to(['messenger/get-conversation']);
$send_msg_url            = Url::to(['messenger/send-msg']);
$conversation_detail_url = Url::to(['messenger/get-conversation-detail']);
$send_ticket_url         = Url::to(['/feedback/create']);
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
var SEND_TICKET_URL = '{$send_ticket_url}';
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
$this->registerJs($js,View::POS_END);
$search_customer_js = <<< JS
var current_chat_id = null;
$(document).on('click', '.chat-left-set-customer-id', function () {
    current_chat_id = $(this).parent().parent().parent().attr('data-id');
    console.log(current_chat_id);
    $.ajax({url: "{$search_customer_url}"})
        .done(function (response) {
            if (response.result && response.result === true && response.view) {
                $('#messenger-modal-content').html(response.view);
                $('#messenger-modal-title').html('Select customer');
                $('#messenger-modal').modal('show');
            } else {
                alert(response.message);
            }
        })
        .fail(function () {
        });
});

$('#chat-send-ticket').on('click', function () {
    var checkedMessages = $('.msg-checkbox:checked');
    if (checkedMessages.length > 0) {
        var data = [];
        for (var i = 0; i < checkedMessages.length; i++) {
            var checkBox = checkedMessages[i];
            console.log(checkBox);
            if (checkBox !== null && checkBox !== undefined) {
                data.push(checkBox.getAttribute('data-id'));
            }
        }
        if (data.length > 0) {
            var params = encodeURIComponent(JSON.stringify(data));
            var url = SEND_TICKET_URL + '?conversation_detail_ids=' + params;
            var win = window.open(url, '_blank');
            win.focus();
        }
    } else {
        alert("Please select messages.");
    }
});

$('#messenger-modal-content').on('click', '.pagination a', function () {
    $.ajax({url: $(this).attr('href')})
        .done(function (response) {
            if (response.result && response.result === true && response.view) {
                $('#messenger-modal-content').html(response.view);
            } else {
                alert(response.message);
            }
        })
        .fail(function () {
        });
    return false;
});

$('#messenger-modal-content').on('beforeSubmit', '#messenger-customer-search-form', function () {
    var form = $(this);
    $.ajax({url: "{$search_customer_url}", data: form.serialize()})
        .done(function (response) {
            if (response.result && response.result === true && response.view) {
                $('#messenger-modal-content').html(response.view);
            } else {
                alert(response.message);
            }
        })
        .fail(function () {
        });
    return false;
});

function setCustomer(id_customer, name) {
    var data = {'conversation_id': current_chat_id, 'id_customer': id_customer};
    $.ajax({url: "{$set_customer_url}", data: data, method: 'POST'})
        .done(function (response) {
            if (response.result && response.result === true) {
                var item = $('.left-chat').find('li[data-id="' + current_chat_id + '"]').find('.chat-left-customer-id:first');
                if (item !== undefined) {
                    item.html(name);
                    $('#messenger-modal').modal('hide');
                }
            } else {
                alert(response.message);
            }
        })
        .fail(function () {
        });
}
JS;
$this->registerJs($search_customer_js,View::POS_END);
