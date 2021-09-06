<?php

use app\models\interfaces\PrizeInterface;
use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this View */
/* @var $prize PrizeInterface */
?>
<div class="row">
    <div class="col-12">
        <h2><?=Yii::t('app', 'Your prize is {prizeTitle}!', ['prizeTitle' => $prize->getTitle()])?></h2>
        <h3><?=Yii::t('app', 'Amount: {amount}', ['amount' => Yii::$app->formatter->asCurrency($prize->getAmount())])?></h3>
        <small><?=$prize->getDescription()?></small>
    </div>
</div>
<div class="d-flex align-items-center justify-content-center">
    <?php
    echo Html::beginForm();
    echo Html::submitButton(Yii::t('app', 'Accept'), [
        'name'  => 'accept',
        'value' => 1,
        'class' => 'btn btn-lg btn-success mb-2 mt-2'
    ]);
    echo Html::endForm();
    ?>
</div>