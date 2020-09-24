<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:40
 *
 */

use tas\social\models\config\ViberConfig;
use tas\social\models\forms\ViberRegisterWebHookForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $viber_hook string */
/* @var $viber_config \tas\social\models\forms\ViberConfigForm */
$registerWebHookModel = new ViberRegisterWebHookForm();
?>

<div class="panel panel-primary">
    <div class="panel-body">
		<?php $form = ActiveForm::begin([
			'options'     => ['class' => 'form-horizontal'],
			'action'      => ['config/update-config','active' => 'viber'],
			'fieldConfig' => [
				'template'     => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-3\">{error}</div><p class=\"hint-block\">{hint}</p>",
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
			],
		]); ?>
        <div class="form-group">
            <label class="control-label col-md-3" for="email"><?=Yii::t('social','Hook Link:')?></label>
            <div class="col-md-9">
                <p class="form-control-static">
					<?=$viber_hook?>
                </p>
            </div>
        </div>
		<?=$form->field($viber_config,'name')->textInput(['maxlength' => true])?>
		<?=$form->field($viber_config,'token')->textInput(['maxlength' => true])?>
        <div class="form-group">
            <div class="col-lg-3"></div>
            <div class="col-lg-6"><?=Html::img($viber_config->avatar,['class' => 'img-reponsive','height' => 50])?></div>
        </div>
		
		<?=$form->field($viber_config,'upload')->fileInput()?>

        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6 text-right">
				<?=Html::submitButton('Update',['class' => 'btn btn-success'])?>
            </div>
        </div>
		<?php ActiveForm::end(); ?>
		<?php if(!empty($viber_config->token)): ?>
			<?php $form = ActiveForm::begin([
				'options'     => ['class' => 'form-horizontal'],
				'action'      => ['config/viber-register-hook','active' => 'viber'],
				'fieldConfig' => [
					'template'     => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-3\">{error}</div><p class=\"hint-block\">{hint}</p>",
					'labelOptions' => ['class' => 'col-lg-3 control-label'],
				],
			]); ?>
            <h3 class="text-center"><?=Yii::t('social.viber','Register Webhook')?></h3>
			<?=Html::activeHiddenInput($registerWebHookModel,'hookUrl',['value' => $viber_hook])?>
			<?=$form->field($registerWebHookModel,'eventTypes')->checkboxList(ViberConfig::eventTypeLabels())?>
			<?=$form->field($registerWebHookModel,'sendName')->checkbox()->label('')?>
			<?=$form->field($registerWebHookModel,'sendPhoto')->checkbox()->label('')?>

            <div class="form-group">
                <div class="col-lg-offset-3 col-lg-6 text-right">
					<?=Html::submitButton('Register',['class' => 'btn btn-success'])?>
                </div>
            </div>
			<?php ActiveForm::end(); ?>
		<?php endif; ?>
    </div>

</div>

