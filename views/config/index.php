<?php

/**
 *
 * User: ThangDang
 * Date: 7/24/18
 * Time: 10:20
 *
 */

use tas\social\models\config\ConfigZalo;

/* @var $this \yii\web\View */
/* @var $zalo_config ConfigZalo */
/* @var $lhc_config \tas\social\models\config\ConfigLHC */
/* @var $facebook_config \tas\social\models\config\ConfigFacebook */
/* @var $zalo_hook string */
/* @var $lhc_hook string */
/* @var $facebook_hook string */
/* @var $viber_hook string */
/* @var $active string */
/* @var $facebook_login_url string */
/* @var $fb_logged bool */
/* @var $moduleConfig \tas\social\models\config\ModuleConfig */
/* @var $viber_config \tas\social\models\forms\ViberConfigForm */


$this->title                   = Yii::t('social','Update Social Configuration');
$this->params['breadcrumbs'][] = ['label' => 'Social Configurations','url' => ['config/index']];


?>
<style>
    .tab-content .panel {
        border-top: none;
        border-radius: 0 0 4px 4px;
        border-color: #ddd;
    }
</style>
<div class="social-configuration-index">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?=($active == 'base'?'active':'')?>">
            <a href="#base" aria-controls="base" role="tab" data-toggle="tab">
				<?=Yii::t('social','Base')?>
            </a>
        </li>
        <li role="presentation" class="<?=($active == 'facebook'?'active':'')?>">
            <a href="#facebook" aria-controls="facebook" role="tab" data-toggle="tab">
				<?=Yii::t('social','Facebook')?>
            </a>
        </li>
        <li role="presentation" class="<?=($active == 'zalo'?'active':'')?>"><a href="#zalo" aria-controls="zalo" role="tab" data-toggle="tab"><?=Yii::t('social','Zalo')?></a></li>
        <li role="presentation" class="<?=($active == 'lhc'?'active':'')?>"><a href="#lhc" aria-controls="lhc" role="tab" data-toggle="tab"><?=Yii::t('social','Live Help Chat')
                ?></a></li>
        <li role="presentation" class="<?=($active == 'viber'?'active':'')?>"><a href="#viber" aria-controls="viber" role="tab" data-toggle="tab"><?=Yii::t('social','Viber')
                ?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?=($active == 'base'?'active':'')?>" id="base">
			<?=$this->render('_base',[
				'moduleConfig' => $moduleConfig,
			])?>
        </div>
        <div role="tabpanel" class="tab-pane <?=($active == 'facebook'?'active':'')?>" id="facebook">
			<?=$this->render('_facebook',[
				'facebook_config'    => $facebook_config,
				'facebook_hook'      => $facebook_hook,
				'facebook_login_url' => $facebook_login_url,
				'fb_logged'          => $fb_logged,
			])?>
        </div>
        <div role="tabpanel" class="tab-pane <?=($active == 'zalo'?'active':'')?>" id="zalo">
			<?=$this->render('_zalo',[
				'zalo_hook'   => $zalo_hook,
				'zalo_config' => $zalo_config,
			])?>
        </div>
        <div role="tabpanel" class="tab-pane <?=($active == 'lhc'?'active':'')?>" id="lhc">
			<?=$this->render('_lhc',[
				'lhc_config' => $lhc_config,
				'lhc_hook'   => $lhc_hook,
			])?>
        </div>
        <div role="tabpanel" class="tab-pane <?=($active == 'viber'?'active':'')?>" id="viber">
			<?=$this->render('_viber',[
				'viber_hook'   => $viber_hook,
				'viber_config' => $viber_config,
			])?>
        </div>
    </div>
</div>

