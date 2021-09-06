<?php

namespace app\models\factory\Prize\type;

use app\jobs\BankTransferJob;
use app\models\base\BaseModel;
use app\models\factory\Prize\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\PrizeLog;
use app\models\UserAccountTransactions;
use app\services\GameService;
use Yii;
use yii\base\InvalidArgumentException;
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
 * @property-read null|string $acceptView
 * @property-read string $type
 */
class PrizeMoney extends BaseModel implements PrizeInterface
{
    //region Class Constants
    /** @var int - send prize amount to user bank account */
    const ACCEPT_TO_BANK = 1;
    /** @var int - transfer prize amount to user local account points */
    const ACCEPT_TO_ACCOUNT = 2;

    const ALLOWED_ACCEPT_TYPES = [
        self::ACCEPT_TO_BANK,
        self::ACCEPT_TO_ACCOUNT,
    ];
    //endregion

    //region Object Attributes
    private int $amount;
    private ?int $acceptType;
    //endregion

    //region General Methods
    /**
     * @inheritdoc
     * @param array $config
     * @param bool $restore - model restoring from hash
     */
    public function __construct($config = [], bool $restore = false)
    {
        parent::__construct($config);
        /** We need to do this only if prize was not creating from hash  */
        if (!$restore) {
            $this->amount = rand(100, 10000);
        }
    }

    /**
     * @param int $acceptType - @see PrizeMoney::ALLOWED_ACCEPT_TYPES
     */
    public function setAcceptType(int $acceptType)
    {
        if (!in_array($acceptType, self::ALLOWED_ACCEPT_TYPES)) {
            throw new InvalidArgumentException("Wrong prize accept type");
        }
        $this->acceptType = $acceptType;
    }

    public function getAcceptType(): ?int
    {
        return $this->acceptType;
    }
    //endregion

    //region Interface Implementation
    /** @inheritdoc */
    public function getView(): ?string
    {
        return '/prize/prize-money';
    }

    /** @inheritdoc */
    public function getAcceptView(): ?string
    {
        return '/prize/accept/prize-money';
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
    public function handleAcceptance(): bool
    {
        if ($this->acceptType === self::ACCEPT_TO_ACCOUNT) {
            return UserAccountTransactions::transferToUser($this->convertMoneyToPoints());
        } elseif ($this->acceptType === self::ACCEPT_TO_BANK) {
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
        $data = json_encode([
            'amount' => $this->amount,
            'type' => PrizeFactory::TYPE_MONEY
        ]);
        return Yii::$app->security->encryptByKey($data, $_ENV['PRIZES_HASH_KEY']);
    }

    /** @inheritdoc */
    public function restore(object $data): void
    {
        if (!isset($data->amount)) {
            throw new InvalidArgumentException(Yii::t('app', 'Data array must contain amount key!'));
        }
        $this->amount = $data->amount;
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

    /**
     * @inheritdoc
     * Game service handles money settings so delegate money reserving to it
     */
    public function reserve(): bool
    {
        $gameService = new GameService();
        return $gameService->reserveMoney($this->amount);
    }

    /**
     * @inheritdoc
     * Game service handles money settings so delegate money restoring to it
     */
    public function release(): bool
    {
        $gameService = new GameService();
        return $gameService->releaseMoney($this->amount);
    }
    //endregion
}