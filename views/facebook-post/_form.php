<?php

use tas\social\SocialAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \tas\social\models\FacebookPost */
/* @var $form yii\widgets\ActiveForm */

SocialAsset::register($this);
?>

<div class="facebook-post-form">
    <div class="panel panel-theme panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?=Html::encode($this->title)?>
            </h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(); ?>
            
            <?=$form->field($model,'message')->textarea(['rows' => 6,'class' => 'form-control form-control-flat'])?>
            
            <?=$form->field($model,'from_name')->textInput(['maxlength' => true,'class' => 'form-control form-control-flat'])?>
            
            <?=$form->field($model,'from_id')->textInput(['maxlength' => true,'class' => 'form-control form-control-flat'])?>
            
            <?=$form->field($model,'created_time')->textInput(['class' => 'form-control form-control-flat'])?>
            
            <?=$form->field($model,'updated_time')->textInput(['class' => 'form-control form-control-flat'])?>
            
            <?=$form->field($model,'post_id')->textInput(['maxlength' => true,'class' => 'form-control form-control-flat'])?>

            <div class="form-group">
                <?=Html::submitButton($model->isNewRecord ? 'Create' : 'Update',['class' => $model->isNewRecord ? 'btn btn-flat btn-success' : 'btn btn-flat
                btn-theme'])?>
            </div>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
