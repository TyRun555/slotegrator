<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_account".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $amount
 *
 * @property User $user
 * @property UserAccountTransactions $userAccountTransaction
 */
class UserAccount extends \app\models\base\BaseAR
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
    public function attributeLabels()
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userAccount');
    }

    /**
     * Gets query for [[UserAccountTransaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccountTransaction()
    {
        return $this->hasOne(UserAccountTransaction::class, ['account_id' => 'id'])->inverseOf('account');
    }
}
