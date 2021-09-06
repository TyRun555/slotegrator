<?php

use app\models\form\PrizeDeliveryForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this \yii\web\View */
/* @var $prize \app\models\interfaces\PrizeInterface */
/* @var $addressForm \app\models\form\PrizeDeliveryForm|null */

$model = $addressForm ?: new PrizeDeliveryForm();
?>
<div class="d-flex align-items-center justify-content-center">
    <span class="text-success">
        <?= Yii::t('app', 'Fill form below and we\'ll ship your prize soon') ?>
    </span>
</div>
<div class="d-flex flex-column align-items-center justify-content-center">
    <?php
    $form = ActiveForm::begin(['action' => 'site/prize-item-delivery']);
    echo $form->field($model, 'country')->textInput();
    echo $form->field($model, 'zip')->textInput();
    echo $form->field($model, 'city')->textInput();
    echo $form->field($model, 'street')->textInput();
    echo $form->field($model, 'building')->textInput();
    echo $form->field($model, 'room')->textInput();
    echo Html::submitButton(Yii::t('app', 'Submit'), [
        'class' => 'btn btn-lg btn-success mt-2 mb-2'
    ]);
    ActiveForm::end();
    ?>
</div>

