<?php

use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $prizeView string */
/** @var $prize \app\models\interfaces\PrizeInterface */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Lucky moment!</h1>
        <p class="lead">You have to sign in before start winning.</p>
        <p><a class="btn btn-lg btn-success" href="<?= Url::to('/site/login') ?>">Sign In</a></p>
    </div>
</div>
