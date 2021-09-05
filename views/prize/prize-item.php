<?php

use yii\bootstrap4\Html;

/* @var $this \yii\web\View */
/* @var $prize \app\models\interfaces\PrizeInterface */
?>
<div class="row">
    <div class="col-12">
        <h2><?= Yii::t('app', 'Your prize is {prizeTitle}!', ['prizeTitle' => $prize->getTitle()]) ?></h2>
        <h3><?= nl2br($prize->getDescription()) ?></h3>
    </div>
</div>
<div class="d-flex align-items-center justify-content-center">
    <?php
    echo Html::beginForm();

    echo Html::submitButton('Accept', [
        'name' => 'accept',
        'value' => 1,
        'class' => 'btn btn-lg btn-success mt-2 mb-2'
    ]);
    echo Html::endForm();
    ?>
</div>
