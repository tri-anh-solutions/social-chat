<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model tas\social\models\AutoReply */

$this->title                   = Yii::t('social','Create Auto Reply');
$this->params['breadcrumbs'][] = ['label' => Yii::t('social','Auto Replies'),'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auto-reply-create">

    <h1><?=Html::encode($this->title)?></h1>
	
	<?=$this->render('_form',[
		'model' => $model,
	])?>

</div>
