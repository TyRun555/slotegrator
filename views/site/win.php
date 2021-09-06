<?php

use app\models\interfaces\PrizeInterface;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $prizeView string */
/* @var $prize PrizeInterface */

$this->title = Yii::t('app', 'You won!');
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Congratulations!</h1>
        <?= $this->render($prize->getView(), compact('prize'))?>
        <?php
        echo Html::beginForm();
        echo Html::submitButton(Yii::t('app', 'Another one'), [
            'name' => 'replay',
            'value' => 1,
            'class' => 'btn btn-lg btn-secondary'
        ]);
        echo Html::endForm();
        ?>
    </div>
</div>

