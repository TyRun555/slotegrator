<?php

namespace app\models;

use app\models\base\BaseAR;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_account".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $amount
 *
 * @property User $user
 * @property-read ActiveQuery $userAccountTransactions
 */
class UserAccount extends BaseAR
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'amount'], 'integer'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['id' => "string", 'user_id' => "string", 'amount' => "string"])]
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'amount' => Yii::t('app', 'Amount'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userAccount');
    }

    /**
     * Gets query for [[UserAccountTransaction]].
     *
     * @return ActiveQuery
     */
    public function getUserAccountTransactions(): ActiveQuery
    {
        return $this->hasOne(UserAccountTransactions::class, ['account_id' => 'id'])->inverseOf('account');
    }
}
