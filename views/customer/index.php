<?php

use app\models\CustomerInfo;
use app\models\TagMapping;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\CustomerInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="customer-info-index">
    
    <?php $form = ActiveForm::begin([
        'id' => 'messenger-customer-search-form',
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
            //'filterModel' => $searchModel,
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
                    'attribute'      => 'Pid',
                    'headerOptions'  => ['class' => 'text-center','style' => 'padding: 20px 5px'],
                    'contentOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute'   => 'fullName',
                    'label'       => 'Full Name <br><div class="vn">Tên Khách Hàng</div>',
                    'encodeLabel' => false,
                ],
                /*
                [
                    'attribute' => 'DTDD',
                ],
                */
                [
                    'attribute'   => 'DTBan',
                    'label'       => 'Phone <br><div class="vn">Điện Thoại</div>',
                    'encodeLabel' => false,
                    'value'       => function ($model){
                        if ($model->DTDD != null) {
                            if ($model->DTDD == null) {
                                return $model->DTBan;
                            }
                            if ($model->DTBan == null) {
                                return $model->DTDD;
                            }
                            if ($model->DTBan != null && $model->DTDD != null) {
                                return $model->DTBan . "; " . $model->DTDD;
                            }
                        }else {
                            return $model->DTBan;
                        }
                    },
                ],
                [
                    'attribute'   => 'DiaChi',
                    'label'       => 'Address <br><div class="vn">Địa Chỉ</div>',
                    'encodeLabel' => false,
                ],
                /*
                [
                    'attribute' => 'DiaChi2',
                ],
                */
                [
                    'class'    => 'kartik\grid\ActionColumn',
                    'header'   => 'Actions <br><div class="vn">Xử Lý</div>',
                    'template' => '{setCustomer}',
                    'buttons'  => [
                        'setCustomer' => function ($url,CustomerInfo $model){
                            return Html::a(Yii::t('social','Select'),'javascript:void(0)',['onClick' => "setCustomer('$model->Pid','$model->fullName')"]);
                        },
                    ],
                
                ],
            ],
        ]);
    }catch (Exception $e){
    } ?>

</div>

