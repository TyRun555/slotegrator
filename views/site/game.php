<?php

use yii\bootstrap4\Html;
use yii\web\View;

/* @var $this View */

$this->title = Yii::t('app', 'Play');
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <?php
        echo Html::beginForm();
        echo Html::submitButton(Yii::t('app', 'Get the prize'), [
            'name' => 'play',
            'value' => 1,
            'class' => 'btn btn-lg btn-success'
        ]);
        echo Html::endForm();
        ?>
    </div>
</div>

