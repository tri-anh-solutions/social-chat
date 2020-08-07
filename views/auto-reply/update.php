<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReply */

$this->title                   = Yii::t('social','Update Auto Reply: {nameAttribute}',[
	'nameAttribute' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('social','Auto Replies'),'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title,'url' => ['view','id' => $model->id_social_auto_reply]];
$this->params['breadcrumbs'][] = Yii::t('social','Update');
?>
<div class="auto-reply-update">
    <h1><?=Html::encode($this->title)?></h1>
	
	<?=$this->render('_form',[
		'model' => $model,
	])?>
</div>
