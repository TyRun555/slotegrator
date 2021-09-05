<?php

namespace app\models\form;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class UserSignUpForm extends Model
{
    public $email;
    public $username;
    public $password;
    public $verifyCode;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
            ['username', 'string', 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username'],
            ['password', 'string', 'min' => 8],
            [['verifyCode'], 'captcha']
        ];
    }

    /**
     * @throws \yii\base\Exception
     */
    public function register(): bool
    {
        if ($this->validate()) {
            $user = new User([
                'username' => $this->username,
                'email' => $this->email
            ]);
            $user->setPassword($this->password);
            if ($user->save(false)) {
                return Yii::$app->user->login($user);
            }
        }
        return false;
    }

}
