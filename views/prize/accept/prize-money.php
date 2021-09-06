<?php

use app\models\factory\Prize\type\PrizeMoney;
use yii\web\View;

/* @var $this View */
/* @var $prize PrizeMoney */
?>
<div class="d-flex align-items-center justify-content-center">
    <span class="text-success">
        <?php if($prize->getAcceptType() === PrizeMoney::ACCEPT_TO_ACCOUNT){ ?>
            <?=Yii::t('app', 'Your account was successfully funded by: {points} points', [
                    'points' => $prize->getAmount()
            ])?>
        <?php } elseif ($prize->getAcceptType() === PrizeMoney::ACCEPT_TO_BANK) { ?>
            <?=Yii::t('app', 'Your bank account will be funded by: {currency} soon', [
                    'currency' => Yii::$app->formatter->asCurrency($prize->getAmount())
            ])?>
        <?php } ?>
    </span>
</div>