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

    public function getView(): ?string
    {
        return '/prize/prize-money';
    }

    public function getType(): string
    {
        return Yii::t('app', 'money');
    }

    public function getTitle(): string
    {
       return Yii::t('app', 'Money');
    }

    public function getDescription(): string
    {
        return Yii::t('app', 'You can choose transfer amount to your bank account or to account points');
    }

    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_MONEY, 'user_id' => Yii::$app->user->id]);
    }

    public function handleReceiving(int $acceptType = null): bool
    {
        if ($acceptType === self::ACCEPT_TO_ACCOUNT) {
            $amountChange = $this->convertToPoints($this->amount);
            return UserAccountTransactions::transferToUser($amountChange);
        } elseif ($acceptType === self::ACCEPT_TO_BANK) {
            $data = ['some_data'];
            /** @var \yii\queue\Queue $queue */
            $queue = Yii::$app->queue;
            $queue->push(new BankTransferJob($data));
            return true;
        }
        return false;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function hash(): string
    {
        return Yii::$app->security->encryptByKey(json_encode(['amount' => $this->amount]), $_ENV['PRIZES_HASH_KEY']);
    }

    public function restoreFromHash(string $hash): string
    {
        $this->amount = json_decode(Yii::$app->security->decryptByKey($hash, $_ENV['PRIZES_HASH_KEY']))['amount'];
    }

    private function convertToPoints(int $amountChange)
    {

    }
}