<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReply */

$this->title                   = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('social', 'Auto Replies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auto-reply-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('social', 'Update'), ['update', 'id' => $model->id_social_auto_reply], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('social', 'Delete'), ['delete', 'id' => $model->id_social_auto_reply], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => Yii::t('social', 'Are you sure you want to delete this item?'),
                'method'  => 'post',
            ],
        ]) ?>
    </p>
    
    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id_social_auto_reply',
            'title',
            'message',
            'reply_content:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
