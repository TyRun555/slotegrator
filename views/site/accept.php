<?php

use app\models\interfaces\PrizeInterface;
use yii\web\View;

/* @var $this View */
/* @var $prize PrizeInterface */

$this->title = 'You won!';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Almost done!</h1>
        <?=$this->render($prize->getAcceptView(), compact('prize'))?>
    </div>
</div>