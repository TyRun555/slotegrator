<?php

namespace app\models;

use app\models\base\BaseAR;
use app\models\factory\Prize\PrizeFactory;
use Yii;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;
use \app\models\UserAccount;

/**
 *
 * @property-read null|string $authKey
 * @property-read int|string $id
 * @property-read UserAccount $account
 *
 * @property int $username [int]
 * @property string $password [varchar(8)]
 * @property string $auth_key [varchar(255)]
 * @property string $access_token [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property int $role [int]
 * @property int $status [int]
 * @property int $created_at [int]
 * @property int $updated_at [int]
 * @property string $email [varchar(255)]
 */
class User extends BaseAR implements IdentityInterface
{
    //region User Roles Constants
    /** @var int - user roles constants */
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    //endregion

    //region User Status Constants
    /** @var int - user status constants */
    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;
    //endregion

    //region Active Record Methods
    public static function tableName(): string
    {
        return '{{%users%}}';
    }

    /**
     * @inheritdoc
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        /**
         * Create account for new user
         */
        if ($insert) {
            $account = new UserAccount([
                'user_id' => $this->id
            ]);
            $account->save();
        }
    }
    //endregion

    //region Active Record Relations
    public function getAccount(): ActiveQuery
    {
        return $this->hasOne(UserAccount::class, ['user_id' => 'id']);
    }
    //endregion

    //region IdentityInterface Methods
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username): ?User
    {
        return self::findOne(['username' => $username]);
    }

    public static function findByPasswordResetToken(string $token): ?User
    {
        return self::findOne(['password_reset_token' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    //endregion

    //region Auth Methods
    /**
     * Sets user password hash
     *
     * @throws \yii\base\Exception
     */
    public function setPassword(string $password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Removes password reset token after password reset
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string|null $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    //endregion
}
