<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:52
 *
 */

use tas\social\models\config\ConfigZalo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $zalo_config ConfigZalo */
/* @var $zalo_hook string */
?>

<?php $form = ActiveForm::begin([
	'options'     => ['class' => 'form-horizontal'],
	'action'      => ['config/update-config','active'=>'zalo'],
	'fieldConfig' => [
		'template'     => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-3\">{error}</div><p class=\"hint-block\">{hint}</p>",
		'labelOptions' => ['class' => 'col-lg-3 control-label'],
	],
]); ?>
<div class="panel panel-primary">
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label col-md-3" for="email">Hook Link:</label>
            <div class="col-md-9">
                <p class="form-control-static">
					<?=$zalo_hook?>
                </p>
            </div>
        </div>
		<?=$form->field($zalo_config,'oa_id')->textInput(['maxlength' => true])?>
		<?=$form->field($zalo_config,'oa_secret')->textInput(['maxlength' => true])?>
		<?=$form->field($zalo_config,'app_id')->textInput(['maxlength' => true])?>
		<?=$form->field($zalo_config,'app_secret')->textInput(['maxlength' => true])?>
		<?=$form->field($zalo_config,'access_token')
		        ->textInput(['maxlength' => true])
		        ->hint(Html::a(Yii::t('social','Get Access Token'),'https://developers.zalo.me/tools/explorer',['target' => '_blank'])
		        )?>
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6 text-right">
			    <?=Html::submitButton('Update',['class' => 'btn btn-success'])?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
