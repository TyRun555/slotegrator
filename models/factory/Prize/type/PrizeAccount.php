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
class PrizeAccount extends BaseModel implements PrizeInterface
{
    private int $amount;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->amount = rand(100, 10000);
    }

    public function getView(): ?string
    {
        return '/prize/prize-account';
    }

    public function getType(): string
    {
        return Yii::t('app', 'points');
    }

    public function getTitle(): string
    {
        return Yii::t('app', 'Account Points');
    }

    public function getDescription(): string
    {
        return Yii::t('app', 'You can transfer prize amount account');
    }

    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_ACCOUNT, 'user_id' => Yii::$app->user->id]);
    }

    public function handleReceiving(int $acceptType = null): bool
    {
        return UserAccountTransactions::transferToUser($this->amount);
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
}