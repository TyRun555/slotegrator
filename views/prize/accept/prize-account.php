<?php

use app\models\interfaces\PrizeInterface;
use yii\web\View;

/* @var $this View */
/* @var $prize PrizeInterface */
?>
<div class="d-flex align-items-center justify-content-center">
    <span class="text-success">
        <?= Yii::t('app', 'Your account was successfully funded by: {points} points', ['points' => $prize->getAmount()]) ?>
    </span>
</div>
