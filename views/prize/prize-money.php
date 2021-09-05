<?php

use app\models\factory\Prize\type\PrizeMoney;
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
    echo Html::hiddenInput('acceptType', PrizeMoney::ACCEPT_TO_BANK);
    echo Html::submitButton(Yii::t('app', 'To bank'), [
        'name' => 'accept',
        'value' => 1,
        'class' => 'btn btn-lg btn-success mt-2 mb-2 mr-2'
    ]);
    echo Html::endForm();
    ?>
    <?php
    echo Html::beginForm();
    echo Html::hiddenInput('acceptType', PrizeMoney::ACCEPT_TO_ACCOUNT);
    echo Html::submitButton(Yii::t('app', 'To points'), [
        'name' => 'accept',
        'value' => 1,
        'class' => 'btn btn-lg btn-warning mt-2 mb-2'
    ]);
    echo Html::endForm();
    ?>
</div>