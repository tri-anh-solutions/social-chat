<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:54
 *
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $lhc_config \tas\social\models\config\ConfigLHC */
/* @var $lhc_hook string */
?>

<?php $form = ActiveForm::begin([
	'options'     => ['class' => 'form-horizontal'],
	'action'      => ['config/update-config','active'=>'lhc'],
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
					<?=$lhc_hook?>
                </p>
            </div>
        </div>
		<?=$form->field($lhc_config,'verify_token')->textInput(['maxlength' => true])?>
		<?=$form->field($lhc_config,'username')->textInput(['maxlength' => true])?>
		<?=$form->field($lhc_config,'token')->textInput(['maxlength' => true])?>
		<?=$form->field($lhc_config,'from_user')->textInput(['maxlength' => true])?>
		<?=$form->field($lhc_config,'callback_url')->textInput(['maxlength' => true])?>
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6 text-right">
			    <?=Html::submitButton('Update',['class' => 'btn btn-success'])?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
