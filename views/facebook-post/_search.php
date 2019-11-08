<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \tas\social\models\FaceboookPostSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="facebook-post-search">
    
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?=$form->field($model,'facebook_post_id')->textInput(['class' => 'form-control-flat form-control'])?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model,'message')->textInput(['class' => 'form-control-flat form-control'])?>
        </div>
        <div class="col-md-4">
            <?=$form->field($model,'from_name')->textInput(['class' => 'form-control-flat form-control'])?>
        </div>
    </div>

    <div class="form-group">
        <?=Html::submitButton('Search',['class' => 'btn btn-theme btn-flat'])?>
        <?=Html::resetButton('Reset',['class' => 'btn btn-default btn-flat'])?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
