<?php

/**
 *
 * User: ThangDang
 * Date: 7/30/18
 * Time: 21:33
 *
 */

/* @var $this \yii\web\View */
/* @var $comments \tas\social\models\FacebookComment[]|mixed */
/* @var $comment \tas\social\models\FacebookComment */
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@app/modules/social/assets');
?>
<?php foreach ($comments as $comment): ?>
    <div class="media comment">
        <div class="media-left">
            <a href="#">
                <img class="media-object" src="<?=$directoryAsset;?>/images/businessman.png" alt="<?=$comment->name;?>" height="30">
            </a>
        </div>
        <div class="media-body">
            <div class="media-content">
                <h5 class="media-heading"><?=$comment->name;?></h5>
                <?php echo $comment->message; ?>
            </div>
            <?=$this->render('_comment',['comments' => $comment->subComments]);?>
        </div>
    </div>
<?php endforeach; ?>