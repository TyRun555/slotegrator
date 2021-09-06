<?php

use app\models\interfaces\PrizeInterface;
use yii\web\View;

/* @var $this View */
/* @var $prize PrizeInterface */
/* @var $addressForm \app\models\form\PrizeDeliveryForm|null */

$this->title = 'You won!';
$addressForm = $addressForm ?? null;
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">You are awesome!</h1>
        <?= $this->render($prize->getAcceptView(), compact('prize', 'addressForm')) ?>
    </div>
</div>