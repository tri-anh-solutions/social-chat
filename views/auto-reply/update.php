<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReply */

$this->title = Yii::t('app', 'Update Auto Reply: ' . $model->title, [
    'nameAttribute' => '' . $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Auto Replies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id_social_auto_reply]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="auto-reply-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
