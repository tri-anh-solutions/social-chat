<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:40
 *
 */


use tas\social\components\FacebookHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $facebook_config \tas\social\models\config\ConfigFacebook */
/* @var $facebook_hook string */
/* @var $facebook_login_url string */
/* @var $fb_logged bool */

$fb_pages            = FacebookHelper::getPages();
$pages               = array_combine(array_column($fb_pages,'id'),array_column($fb_pages,'name'));
$login_callback_url  = Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->controller->module->id . '/config/facebook-login']);
$logout_callback_url = Yii::$app->urlManager->createAbsoluteUrl([Yii::$app->controller->module->id . '/config/facebook-logout']);
?>
<?php $form = ActiveForm::begin([
	'options'     => ['class' => 'form-horizontal'],
	'action'      => ['config/update-config','active'=>'facebook'],
	'fieldConfig' => [
		'template'     => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-3\">{error}</div><p class=\"hint-block\">{hint}</p>",
		'labelOptions' => ['class' => 'col-lg-3 control-label'],
	],
]); ?>
    <div class="panel panel-primary">
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label col-md-3" for="email"><?=Yii::t('social','Hook Link')?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
						<?=$facebook_hook;?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3" for="email"><?=Yii::t('social','Login callback URL')?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
						<?=$login_callback_url;?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3" for="email"><?=Yii::t('social','Logout callback URL')?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
						<?=$logout_callback_url?>
                    </p>
                </div>
            </div>
			<?=$form->field($facebook_config,'verify_token')->textInput(['maxlength' => true])?>
			<?php if(!empty($facebook_config->app_id) && !empty($facebook_config->app_secret)): ?>
				<?php if(!$fb_logged): ?>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="email"><?=Yii::t('social','Authorize')?>:</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
								<?=$facebook_login_url;?>
                            </p>
                        </div>
                    </div>
				<?php else: ?>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="email"><?=Yii::t('social','Unauthorized')?>:</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
								<?=Html::a('Unauthorized',['config/facebook-logout']);?>
                            </p>
                        </div>
                    </div>
					<?=$form->field($facebook_config,'page_id')->dropDownList($pages,[
					'id'     => 'fb-page-id',
					'prompt' => Yii::t('social','Select page'),
				])?>
                    <div class="form-group">
                        <label class="control-label col-md-3" for="email"><?=Yii::t('social','Webhook')?>:</label>
                        <div class="col-md-9">
                            <p class="form-control-static">
                                <button id="subscribe-web-hook" class="btn btn-primary" type="button"><i class="fa fa-spinner fa-pulse hidden"></i> <span></span></button>
                            </p>
                        </div>
                    </div>
					<?php echo $form->field($facebook_config,'page_token')->textInput(['id' => 'fb-page-token','maxlength' => true]) ?>
					<?php echo $form->field($facebook_config,'long_page_token')->textInput(['id' => 'fb-long-page-token','maxlength' => true]) ?>
				<?php endif; ?>
			<?php endif; ?>
			<?=$form->field($facebook_config,'app_id')->textInput(['id' => 'fb-app-id','maxlength' => true])?>
			<?=$form->field($facebook_config,'app_secret')->passwordInput(['maxlength' => true])?>
            <div class="form-group">
                <div class="col-lg-offset-3 col-lg-6 text-right">
					<?=Html::submitButton('Update',['class' => 'btn btn-success'])?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php

$get_page_token_url = Url::to(['config/page-access-token']);
$app_subscribe_url  = Url::to(['config/subscribed-apps']);
$msg                = json_encode([
	'subscribe'   => Yii::t('social','Subscribe'),
	'unsubscribe' => Yii::t('social','Unsubscribe'),
]);
$js                 = <<<JS
var msg = {
    'subscribe':'',
    'unsubscribe':''
};
$.extend(msg,{$msg});

var hook_action = 'create';
$('#fb-page-id').change(function () {
    var page_id = $(this).val();
    $('#subscribe-web-hook').hide();
    $.ajax({
        url: '{$get_page_token_url}',
        data: {'page_id': page_id},
        success: function (data) {
            console.log(data);
            $('#fb-page-token').val(data);
            checkSubscribe();
        }
    });
});

function checkSubscribe() {
    var app_id = $('#fb-app-id').val();
    var page_id = $('#fb-page-id').val();
    if ($('#fb-page-id').length > 0 && page_id != '') {
        $('#subscribe-web-hook').attr('disabled',true);
        $('#subscribe-web-hook i').removeClass('hidden');
        $.ajax({
            url: '{$app_subscribe_url}',
            data: {'page_id':page_id},
            'success': function (data) {
                console.log(data);
                $('#subscribe-web-hook').show().removeAttr('disabled');
                $('#subscribe-web-hook i').addClass('hidden');
                // console.log(data.data);
                $('#subscribe-web-hook span').text(msg.subscribe).show();
                if (data.success && data.data) {
                    for (var i in data.data) {
                        var app = data.data[i];
                        console.log(app.id);
                        console.log(app_id);
                        if (app.id == app_id) {
                            $('#subscribe-web-hook span').text(msg.unsubscribe).show();
                            hook_action = 'delete';
                            return;
                        }
                    }
                }else{
                    alert(data.error);
                }
                
            },
            'dataType': 'json'
        });
    } else {
        $('#subscribe-web-hook span').text(msg.subscribe);
        hook_action = 'create';
    }
}

$('#subscribe-web-hook').click(function () {
    var page_id = $('#fb-page-id').val();
    $('#subscribe-web-hook i').removeClass('hidden');
    $('#subscribe-web-hook').attr('disabled',true);
    $.ajax({
        url: '{$app_subscribe_url}',
        data: {'action': hook_action,'page_id':page_id},
        'success': function (data) {
            console.log(data);
            console.log(data.data);
            if (data.success) {
                if (hook_action == 'create') {
                    hook_action = 'delete';
                    $('#subscribe-web-hook span').text(msg.unsubscribe);
                } else {
                    hook_action = 'create';
                    $('#subscribe-web-hook span').text(msg.subscribe);
                }
            }else{
                $('#subscribe-web-hook span').text(msg.subscribe);
                hook_action = 'create';
            }
            $('#subscribe-web-hook i').addClass('hidden');
            $('#subscribe-web-hook').removeAttr('disabled');
        },
        'dataType': 'json'
    });
});

checkSubscribe();
JS;
$this->registerJs($js);