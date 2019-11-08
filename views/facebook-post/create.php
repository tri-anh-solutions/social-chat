<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\FacebookPost */

$this->title = 'Create Facebook Post';
$this->params['breadcrumbs'][] = ['label' => 'Facebook Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facebook-post-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
