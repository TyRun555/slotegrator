<?php

namespace app\models;

use app\models\base\BaseAR;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_account_transactions".
 *
 * @property int $id
 * @property int $account_id
 * @property int|null $created_at
 * @property int|null $amount_change
 * @property int|null $type
 * @property int|null $direction
 * @property-read UserAccount $account
 */
class UserAccountTransactions extends BaseAR
{
    //region Type Constants
    /** @var int - increase user account amount  */
    const TYPE_INCREASE = 1;
    /** @var int - decrease user account amount  */
    const TYPE_DECREASE = 2;
    //endregion

    //region Direction Constants
    /** @var int - transaction for wining the prize  */
    const DIRECTION_FROM_SERVICE_TO_USER = 1;
    //endregion

    //region Active Record Methods
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'user_account_transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['account_id'], 'required'],
            [['account_id', 'created_at', 'amount_change', 'type', 'direction'], 'integer'],
            [['account_id'], 'unique'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAccount::class, 'targetAttribute' => ['account_id' => 'id']],
            ['amount_change', 'validateAmountChange', 'when' => function(self $model) {
                return $model->type === self::TYPE_DECREASE;
            }]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account_id' => Yii::t('app', 'Account ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'amount_change' => Yii::t('app', 'Amount Change'),
            'type' => Yii::t('app', 'Type'),
            'direction' => Yii::t('app', 'Direction'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $amount_change = 0;
            switch ($this->type) {
                case UserAccountTransactions::TYPE_INCREASE:
                    $amount_change = $this->account->amount += $this->amount_change;
                    break;
                case UserAccountTransactions::TYPE_DECREASE:
                    $amount_change = $this->account->amount -= $this->amount_change;
                    break;
            }
            $this->account->amount = $amount_change;
            $this->account->save(false);
        }
    }
    //endregion

    //region Relations
    /**
     * Gets query for [[Account]].
     *
     * @return ActiveQuery
     */
    public function getAccount(): ActiveQuery
    {
        return $this->hasOne(UserAccount::class, ['id' => 'account_id'])->inverseOf('userAccountTransactions');
    }
    //endregion

    //region Validators
    /**
     * Check for insufficient account points when try to save decrease type transaction
     *
     * @param string $attribute
     * @param array $params
     */
    public function validateAmountChange(string $attribute, array $params)
    {
        if ($this->amount_change > $this->account->amount) {
            $this->addError($attribute, 'Insufficient account points');
        }
    }
    //endregion

    //region Domain Methods
    /**
     * @param int $amountChange - amount to change current user account amount
     * @return bool
     */
    public static function transferToUser(int $amountChange): bool
    {
        $transaction = new self([
            'account_id' => Yii::$app->user->identity->account->id,
            'type' => UserAccountTransactions::TYPE_INCREASE,
            'direction' => UserAccountTransactions::DIRECTION_FROM_SERVICE_TO_USER,
            'amount_change' => $amountChange
        ]);
        return $transaction->save(false);
    }
    //endregion
}
