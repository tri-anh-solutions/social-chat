<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReply */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auto-reply-form">
    
    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'message')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'reply_content')->textarea(['rows' => 6])->hint('
    {sender_name}: Sender name, {receiver_name}: Receiver name
    ') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
