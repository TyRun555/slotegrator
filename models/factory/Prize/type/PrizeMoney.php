<?php

namespace app\models\factory\Prize\type;

use app\jobs\BankTransferJob;
use app\models\base\BaseModel;
use app\models\factory\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\PrizeLog;
use app\models\UserAccountTransactions;
use Yii;
use yii\db\ActiveQuery;

/**
 * Represents money prize
 * Could be converted to point and supplement to account
 * or transferred directly to bank account
 *
 * @property-read ActiveQuery $log
 * @property-read string $description
 * @property-read string $title
 * @property-read null|string $view
 * @property-read string $type
 */
class PrizeMoney extends BaseModel implements PrizeInterface
{
    //region Class Constants
    /** @var int - send prize amount to user bank account */
    const ACCEPT_TO_BANK = 1;
    /** @var int - transfer prize amount to user local account points */
    const ACCEPT_TO_ACCOUNT = 1;
    //endregion

    private int $amount;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->amount = rand(100, 10000);
    }

    /** @inheritdoc */
    public function getView(): ?string
    {
        return '/prize/prize-money';
    }

    /** @inheritdoc */
    public function getType(): string
    {
        return Yii::t('app', 'money');
    }

    /** @inheritdoc */
    public function getTitle(): string
    {
       return Yii::t('app', 'Money');
    }

    /** @inheritdoc */
    public function getDescription(): string
    {
        return Yii::t('app', 'You can choose transfer amount to your bank account or to account points');
    }

    /** @inheritdoc */
    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_MONEY, 'user_id' => Yii::$app->user->id]);
    }

    /** @inheritdoc */
    public function handleReceiving(int $acceptType = null): bool
    {
        if ($acceptType === self::ACCEPT_TO_ACCOUNT) {
            return UserAccountTransactions::transferToUser($this->convertMoneyToPoints());
        } elseif ($acceptType === self::ACCEPT_TO_BANK) {
            $data = ['amount' => $this->getAmount()];
            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;
            $queue->push(new BankTransferJob($data));
            return true;
        }
        return false;
    }

    /** @inheritdoc */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /** @inheritdoc */
    public function hash(): string
    {
        return Yii::$app->security->encryptByKey(json_encode(['amount' => $this->amount]), $_ENV['PRIZES_HASH_KEY']);
    }

    /** @inheritdoc */
    public function restoreFromHash(string $hash): void
    {
        $this->amount = json_decode(Yii::$app->security->decryptByKey($hash, $_ENV['PRIZES_HASH_KEY']))['amount'];
    }

    /**
     * Converts money amount to pints using multiplier from .env.local
     *
     * @return int
     */
    private function convertMoneyToPoints(): int
    {
        return (int)($this->amount * $_ENV['MONEY_TO_ACCOUNT_CONVERSION_MULTIPLIER']);
    }
}