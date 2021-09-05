<?php
/* @var $this \yii\web\View */

use yii\bootstrap4\Html;


?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <?php
        echo Html::beginForm();
        echo Html::submitButton('Get the prize', [
            'name' => 'play',
            'value' => 1,
            'class' => 'btn btn-lg btn-success'
        ]);
        echo Html::endForm();
        ?>
    </div>
</div>

