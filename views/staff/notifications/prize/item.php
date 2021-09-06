<?php

/* @var $this \yii\web\View */
/** @var $notification \app\models\StaffNotification */
?>
<h1>
    <?= Yii::t('app', "User {username} (#{userid}) has won a prize!", [
        'username' => $notification->user->username,
        'userid' => $notification->user->id
    ]) ?>
</h1>
<p><?= Yii::t('app', "Date: ") . Yii::$app->formatter->asDatetime($notification->created_at, 'full') ?></p>
<p><?= Yii::t('app', "Prize data") ?></p>
<table>
    <tbody>
    <?php foreach ($notification->data ?: [] as $key => $value) { ?>
        <tr>
            <td><?= $key . ":" ?></td>
            <td><?= $value ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>