<?php

use app\models\form\UserLoginForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model UserLoginForm */


$this->title                   = Yii::t('app', 'Sign In');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?=Html::encode($this->title)?></h1>

    <p>Please fill out the following fields to sign in:</p>
    
    <?php $form = ActiveForm::begin([
        'id'          => 'login-form',
        'layout'      => 'horizontal',
        'fieldConfig' => [
            'template'     => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 col-form-label'],
        ],
    ]); ?>
    
    <?=$form->field($model, 'username')->textInput(['autofocus' => true])?>
    
    <?=$form->field($model, 'password')->passwordInput()?>
    
    <?=$form->field($model, 'rememberMe')->checkbox([
        'template' => "<div class=\"offset-lg-1 col-lg-3 custom-control custom-checkbox\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
    ])?>

    <div class="form-group">
        <div class="offset-lg-1 col-lg-11">
            <?=Html::submitButton(Yii::t('app', 'Sign in'), ['class' => 'btn btn-primary', 'name' => 'login-button'])?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="row">
    <div class="col-12">
        <a href="<?=Url::to(['user/request-reset-password'])?>"><?=Yii::t('app', 'Forgot password?')?></a><br>
        <a href="<?=Url::to(['user/sign-up'])?>"><?=Yii::t('app', 'Sign up')?></a>
    </div>
</div>
