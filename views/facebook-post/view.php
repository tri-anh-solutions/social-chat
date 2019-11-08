<?php

use tas\social\models\FacebookPost;
use tas\social\SocialAsset;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model FacebookPost */

$this->title                   = $model->facebook_post_id;
$this->params['breadcrumbs'][] = ['label' => 'Facebook Posts','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

SocialAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@app/modules/social/assets');
?>
<div class="facebook-post-view">

    <div class="panel panel-theme panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?=Html::encode($this->title)?>
            </h3>
        </div>
        <div class="panel-body">
            <p>
                <?=Html::a('Update',['update','id' => $model->facebook_post_id],['class' => 'btn btn-theme btn-flat'])?>
                <?=Html::a('Delete',['delete','id' => $model->facebook_post_id],[
                    'class' => 'btn btn-danger btn-flat',
                    'data'  => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method'  => 'post',
                    ],
                ])?>
            </p>
            
            <?=DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'facebook_post_id',
                    'message:ntext',
                    'from_name',
                    'from_id',
                    'created_time',
                    'updated_time',
                    'post_id',
                ],
            ])?>
            <h3 class="page-header">
                <?=Yii::t('app','Comments')?>
            </h3>
            <div class="media comment">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object" src="<?=$directoryAsset;?>/images/businessman.png" alt="<?=$model->from_name;?>" height="30">
                    </a>
                </div>
                <div class="media-body">
                    <div class="media-content">
                        <h5 class="media-heading"><?=$model->from_name;?></h5>
                        <?=$model->message;?>
                    </div>
                    <?=$this->render('_comment',['comments' => $model->comments]);?>
                </div>
            </div>
        </div>
    </div>
</div>
