<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FacebookPost */

$this->title = 'Update Facebook Post: ' . $model->facebook_post_id;
$this->params['breadcrumbs'][] = ['label' => 'Facebook Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->facebook_post_id, 'url' => ['view', 'id' => $model->facebook_post_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="facebook-post-update">
    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
