<?php
/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Account');
$accountPoints = Yii::$app->user->identity->account->amount;
?>
<h1> <?= Yii::t('app', 'Account history') ?></h1>

<h2>
    <?= Yii::t('app', 'Amount: {points} points', ['points' => $accountPoints]) ?>
</h2>
<p><?= Yii::t('app', 'See your account history here soon') ?></p>
