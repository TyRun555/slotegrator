<?php

namespace app\models\form;

use app\models\User;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\Model;
use yii\web\Cookie;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class UserLoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private ?User $_user;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['password', 'username'], 'required'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    #[ArrayShape(['password' => "string", 'username' => "string", 'rememberMe' => "string"])]
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('app', 'Password'),
            'username' => Yii::t('app', 'Username'),
            'rememberMe' => Yii::t('app', 'Remember me'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     */
    public function validatePassword(string $attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Wrong password'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        if ($this->validate()) {

            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? Yii::$app->params['user.sessionDuration'] : 0);
        }

        return false;
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser(): ?User
    {
        $this->_user = User::findOne(['username' => $this->username, 'status' => User::STATUS_ACTIVE, 'role' => User::ROLE_USER]);

        return $this->_user;
    }

}
