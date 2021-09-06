<?php

use app\models\interfaces\PrizeInterface;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $prizeView string */
/** @var $prize PrizeInterface */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4"><?= Yii::t('app', 'Lucky moment!') ?></h1>
        <p class="lead"><?= Yii::t('app', 'You have to sign in before start winning.') ?></p>
        <p><a class="btn btn-lg btn-success" href="<?= Url::to('/site/login') ?>"><?= Yii::t('app', 'Sign in') ?></a>
        </p>
    </div>
</div>
