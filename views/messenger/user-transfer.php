<?php
use app\models\CustomerInfo;
use app\models\User;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use tas\social\models\UserSearch;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php $form = ActiveForm::begin([
	'id' => 'social-user-search-form',
]); ?>

<div class="row">
	<div class="col-md-12">
		<?php echo $form->field($searchModel,'keyword')->label(false)->textInput(['placeholder' => Yii::t('social','Keyword...')]) ?>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<?=Html::submitButton('Search',['class' => 'btn btn-info'])?>
	
	</div>
</div>
<?php ActiveForm::end(); ?>
<hr>
<?php
try{
	echo GridView::widget([
		'pager'        => [
			'firstPageLabel' => 'First',
			'lastPageLabel'  => 'Last',
			'maxButtonCount' => 7,
		],
		'dataProvider' => $dataProvider,
		'responsive'   => true,
		'floatHeader'  => false,
		'hover'        => true,
		'export'       => false,
		'toolbar'      => [
			[
				'content' => '',
			],
			'{export}',
			'{toggleData}',
		],
		'panel'        => [],
		'columns'      => [
			[
				'attribute'      => 'id',
			],
			[
				'attribute'   => 'username',
			],[
				'attribute'   => 'full_name',
			],[
				'attribute'   => 'email',
			],
			[
				'class'    => 'kartik\grid\ActionColumn',
				'template' => '{setCustomer}',
				'buttons'  => [
					'setCustomer' => function($url,User $model){
						return Html::a(Yii::t('social','Transfer'),'javascript:void(0)',[
							'class' => 'select-user',
							'data'  => [
								'id'        => $model->id,
							],
						]);
					},
				],
			
			],
		],
	]);
}
catch(Exception $e){
	Yii::error($e);
} ?>
