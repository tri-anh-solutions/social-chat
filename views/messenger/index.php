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

/* @var $this \yii\web\View */

$this->title                   = Yii::t('social','Conversations');
$this->params['breadcrumbs'][] = $this->title;
SocialAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@tas/social/assets');

?>
    <div class="conversation-index">
        <div id="chatApp" class="container-fluid main-section">
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
        </div>
    </div>
    <div id="user-template" style="display: none;">
        <li>
            <div class="chat-left-img">
                <img src="">
            </div>
            <div class="chat-left-detail">
                <p class="full-name"></p>
                <div>
                    <span class="badge chat-left-unread-count"></span>
                    <span class="chat-left-updated-at"></span>
                    <span class="chat-left-locked-by"></span>
                    <span class="chat-left-lock"><i class="fa fa-lock"></i> </span>
                    <span class="chat-left-unlock"><i class="fa fa-unlock-alt"></i> </span>
                    <span class="chat-left-transfer"><i class="fa fa-exchange"></i> </span>
                </div>
				<?php if(class_exists('app\models\CustomerInfo')): ?>
                    <div class="chat-left-customer-info">
                        <span class="chat-left-customer-name">
                        </span>
                        <a href="javascript:void(0)" class="chat-left-set-customer-id" data-id="">
							<?=Yii::t('app','Set customer')?>
                        </a>
                    </div>
				<?php endif; ?>
            </div>
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

$options = json_encode([
	'urls'     => [
		'conversation'       => Url::to(['messenger/get-conversation']),
		'conversationDetail' => Url::to(['messenger/get-conversation-detail']),
		'sendTicket'         => Url::to(['/feedback/create']),
		'sendMsg'            => Url::to(['messenger/send-msg']),
		'searchCustomer'     => Url::to(['messenger/search-customers']),
		'setCustomer'        => Url::to(['messenger/set-customer']),
		'lock'               => Url::to(['messenger/ajax-lock']),
		'unlock'             => Url::to(['messenger/ajax-unlock']),
		'debug'              => YII_ENV_DEV,
	],
	'chatType' => [
		'facebook' => Conversation::TYPE_FACEBOOK,
		'viber'    => Conversation::TYPE_VIBER,
		'zalo'     => Conversation::TYPE_ZALO,
		'lhc'      => Conversation::TYPE_LHC,
	],
	'logo'     => [
		'facebook' => "$directoryAsset/images/facebook.jpg",
		'viber'    => "$directoryAsset/images/viber.jpg",
		'zalo'     => "$directoryAsset/images/zalo.jpg",
		'lhc'      => "$directoryAsset/images/livechat.png",
	],
	'msgType'  => [
		'text' => ConversationDetail::TYPE_TEXT,
		'img'  => ConversationDetail::TYPE_IMG,
		'link' => ConversationDetail::TYPE_LINK,
	],
	'modal'    => '#messenger-modal',
]);


$js = <<<JS
new $.Chat({$options});
JS;
$this->registerJs($js);
