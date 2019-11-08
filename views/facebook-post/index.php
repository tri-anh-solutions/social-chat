<?php

use tas\social\models\FaceboookPostSearch;
use tas\social\SocialAsset;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel FaceboookPostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

SocialAsset::register($this);
$this->title                   = 'Facebook Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facebook-post-index">
    <div class="panel panel-theme panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?=Html::encode($this->title)?>
            </h3>
        </div>
        <div class="panel-body">
            <?php echo $this->render('_search',['model' => $searchModel]); ?>
            <hr>
            <?=GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel'  => $searchModel,
                'columns'      => [
                    ['class' => 'yii\grid\SerialColumn'],
                    // 'facebook_post_id',
                    //'encryptId',
                    'message:ntext',
                    'from_name',
                    // 'from_id',
                    'created_time',
                    // 'updated_time',
                    // 'post_id',
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'contentOptions' => [
                                'class'=>'text-center'
                        ]
                    ],
                ],
            ]);?>
        </div>
    </div>
</div>
