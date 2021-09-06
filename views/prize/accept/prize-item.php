<?php

use yii\bootstrap4\Html;

/* @var $this \yii\web\View */
/* @var $prize \app\models\interfaces\PrizeInterface */
?>
<div class="d-flex align-items-center justify-content-center">
    <span class="text-success">
        <?=Yii::t('app', 'Fill form below and we\'ll ship your prize soon')?>
    </span>
</div>
<div class="d-flex align-items-center justify-content-center">
    <?php
    echo Html::beginForm();
    echo Html::textInput('country');
    echo Html::textInput('zip');
    echo Html::textInput('city');
    echo Html::textInput('street');
    echo Html::textInput('building');
    echo Html::textInput('room');
    echo Html::submitButton(Yii::t('app',  'Submit'), [
        'class' => 'btn btn-lg btn-success mt-2 mb-2'
    ]);
    echo Html::endForm();
    ?>
</div>

