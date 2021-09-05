<?php

namespace app\models\form;

use app\models\base\BaseModel;
use app\models\User;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class UserRequestPasswordResetForm extends BaseModel
{
    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    #[ArrayShape(['email' => "string"])]
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was sent
     * @throws \yii\base\Exception
     */
    public function sendEmail(): bool
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                $this->addError('email', Html::errorSummary($user));
                return false;
            }
        }

        $link = Html::a('here', Url::to(["/user/reset-password/", 'token' =>  $user->password_reset_token]), ['target' => "_blank"]);

        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['supportEmail'])
            ->setTo($user->email)
            ->setSubject(Yii::$app->name . ": password reset")
            ->setHtmlBody("Click " . $link . " to reset your password")
            ->send();
    }
}
