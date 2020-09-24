<?php
/**
 *
 * User: ThangDang
 * Date: 9/23/20
 * Time: 21:49
 *
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $moduleConfig \tas\social\models\config\ModuleConfig */
?>

<?php $form = ActiveForm::begin([
	'options'     => ['class' => 'form-horizontal'],
	'action'      => ['config/update-config'],
	'fieldConfig' => [
		'template'     => "{label}\n<div class=\"col-lg-6\">{input}</div>\n<div class=\"col-lg-3\">{error}</div><p class=\"hint-block\">{hint}</p>",
		'labelOptions' => ['class' => 'col-lg-3 control-label'],
	],
]); ?>
<div class="panel panel-primary">
    <div class="panel-body">
		<?=$form->field($moduleConfig,'auto_reply')->checkbox()->label('')?>
        <div class="form-group">
            <div class="col-lg-offset-3 col-lg-6 text-right">
				<?=Html::submitButton('Update',['class' => 'btn btn-success'])?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
	