<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel tas\social\models\AutoReplySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = Yii::t('social','Auto Replies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auto-reply-index">

    <h1><?=Html::encode($this->title)?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?=Html::a(Yii::t('social','Create Auto Reply'),['create'],['class' => 'btn btn-success'])?>
    </p>
	
	<?=GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'columns'      => [
			['class' => 'yii\grid\SerialColumn'],
			// 'id_social_auto_reply',
			'title',
			'message',
			'reply_content:ntext',
			'created_at:datetime',
			//'updated_at',
			['class' => ActionColumn::class],
		],
	]);?>
</div>
