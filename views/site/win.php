<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $prizeView string */
/* @var $prize \app\models\interfaces\PrizeInterface */

?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Congratulations!</h1>
        <?= $this->render($prizeView, compact('prize'))?>
        <?php
        echo Html::beginForm();
        echo Html::submitButton('Another one', [
            'name' => 'play',
            'value' => 1,
            'class' => 'btn btn-lg btn-secondary'
        ]);
        echo Html::endForm();
        ?>
    </div>
</div>

