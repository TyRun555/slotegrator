<?php

namespace app\models\form;

use InvalidArgumentException;
use Throwable;
use Yii;
use yii\base\Model;
use app\models\User;

/**
 * Password reset form
 *
 * @property-read \app\models\User|null $user
 */
class UserResetPasswordForm extends Model
{
    public $password;
    public $passwordRepeat;

    private User $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(Yii::t('app', 'Password reset token cannot be blank.'));
        }
        try {
            $this->_user = User::findByPasswordResetToken($token);
        } catch (Throwable) {
            throw new InvalidArgumentException(Yii::t('app', 'Wrong password reset token.'));
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password', 'passwordRepeat'], 'required'],
            ['password', 'string', 'min' => 8],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'passwordRepeat' => Yii::t('app', 'Repeat password'),
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     * @throws \yii\base\Exception
     */
    public function resetPassword(): bool
    {
        if ($this->validate()) {
            $user = $this->_user;
            $user->setPassword($this->password);
            $user->removePasswordResetToken();
            return $user->save(false);
        }
        return false;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }
}
