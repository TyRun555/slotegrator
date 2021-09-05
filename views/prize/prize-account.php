<?php

use yii\bootstrap4\Html;

/* @var $this \yii\web\View */
/* @var $prize \app\models\interfaces\PrizeInterface */
?>
<div class="row">
    <div class="col-12">
        <h2><?= Yii::t('app', 'Your prize is {prizeTitle}!', ['prizeTitle' => $prize->getTitle()]) ?></h2>
        <h3><?= Yii::t('app', 'Amount: {amount}', ['amount' => Yii::$app->formatter->asCurrency($prize->getAmount())])?></h3>
        <small><?= $prize->getDescription() ?></small>
    </div>
</div>
<div class="d-flex align-items-center justify-content-center">
    <?php
    echo Html::beginForm();
    echo Html::submitButton('Accept', [
        'name' => 'accept',
        'value' => 1,
        'class' => 'btn btn-lg btn-success mb-2 mt-2'
    ]);
    echo Html::endForm();
    ?>
</div>