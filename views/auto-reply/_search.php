<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReplySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auto-reply-search">
	
	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>
	
	<?=$form->field($model,'title')?>
	
	<?=$form->field($model,'message')?>
	
	<?=$form->field($model,'reply_content')?>
    
    <div class="form-group">
		<?=Html::submitButton(Yii::t('social','Search'),['class' => 'btn btn-primary'])?>
		<?=Html::resetButton(Yii::t('social','Reset'),['class' => 'btn btn-default'])?>
    </div>
	
	<?php ActiveForm::end(); ?>

</div>
