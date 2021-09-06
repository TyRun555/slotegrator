<?php

namespace app\controllers;

use app\controllers\base\BaseController;
use app\models\form\UserRequestPasswordResetForm;
use app\models\form\UserResetPasswordForm;
use app\models\form\UserSignUpForm;
use app\models\User;
use Yii;
use yii\web\Response;

class UserController extends BaseController
{
    /**
     * @throws \yii\base\Exception
     */
    public function actionSignUp(): Response|string
    {
        $model = new UserSignUpForm();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->register()) {
                    return $this->goHome();
                }
            }
        }
        return $this->render('signup', compact('model'));
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionRequestResetPassword(): Response|string
    {
        if (!Yii::$app->user->isGuest) Yii::$app->user->logout();
        $model = new UserRequestPasswordResetForm();
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
                return $this->render('request-reset-password-success');
            }

        }
        return $this->render('request-reset-password', compact('model'));
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionResetPassword(string $token): Response|string
    {
        if (!Yii::$app->user->isGuest) Yii::$app->user->logout();

        try {
            $model = new UserResetPasswordForm($token);
            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                $model->resetPassword();
                if (Yii::$app->user->login($model->user)) {
                    return $this->render('reset-password-success');
                }
            }
        } catch (\InvalidArgumentException $e) {
            return $this->render('/site/error', [
                'message' => $e->getMessage(),
                'name' => 'Error'
            ]);
        }

        return $this->render('reset-password', compact('model'));
    }

}
