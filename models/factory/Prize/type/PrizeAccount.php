<?php

namespace app\models\factory\Prize\type;

use app\jobs\BankTransferJob;
use app\models\base\BaseModel;
use app\models\factory\Prize\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\PrizeLog;
use app\models\UserAccountTransactions;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;

/**
 * Represents account points prize
 * Supplements user account
 *
 * @property-read ActiveQuery $log
 * @property-read string $description
 * @property-read string $title
 * @property-read null|string $view
 * @property-read string $type
 */
class PrizeAccount extends BaseModel implements PrizeInterface
{
    private int $amount;

    
    public function __construct($config = [], bool $restore = false)
    {
        parent::__construct($config);
        /** We need to do this only if prize was not creating from hash  */
        if (!$restore) {
            $this->amount = rand(100, 10000);
        }
    }
    
    /** @inheritdoc  */
    public function getView(): ?string
    {
        return '/prize/prize-account';
    }
    
    /** @inheritdoc  */
    public function getAcceptView(): ?string
    {
        return '/prize/accept/prize-account';
    }
    
    /** @inheritdoc  */
    public function getType(): string
    {
        return Yii::t('app', 'points');
    }
    
    /** @inheritdoc  */
    public function getTitle(): string
    {
        return Yii::t('app', 'Account Points');
    }
    
    /** @inheritdoc  */
    public function getDescription(): string
    {
        return Yii::t('app', 'You can transfer prize amount account');
    }
    
    /** @inheritdoc  */
    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_ACCOUNT, 'user_id' => Yii::$app->user->id]);
    }
    
    /** @inheritdoc  */
    public function handleAcceptance(int $acceptType = null): bool
    {
        return UserAccountTransactions::transferToUser($this->amount);
    }
    
    /** @inheritdoc  */
    public function getAmount(): int
    {
        return $this->amount;
    }
    
    /** @inheritdoc  */
    public function hash(): string
    {
        $data = json_encode([
            'amount' => $this->amount,
            'type' => PrizeFactory::TYPE_ACCOUNT
        ]);
        return Yii::$app->security->encryptByKey($data, $_ENV['PRIZES_HASH_KEY']);
    }
    
    /** @inheritdoc  */
    public function restore(object $data): void
    {
        if (!isset($data->amount)) {
            throw new InvalidArgumentException(Yii::t('app', 'Data array must contain amount key!'));
        }
        $this->amount = $data->amount;
    }
    
    /** @inheritdoc  */
    public function reserve(): bool
    {
        return true;
    }
    
    /** @inheritdoc  */
    public function release(): bool
    {
        return true;
    }
}