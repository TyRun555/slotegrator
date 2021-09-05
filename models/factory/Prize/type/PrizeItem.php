<?php

namespace app\models\factory\Prize\type;

use app\models\base\BaseModel;
use app\models\factory\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\Prize;
use app\models\PrizeLog;
use app\models\StaffNotification;
use app\models\UserAccountTransactions;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * Represents item prize
 * Manually processing with sending by post
 * Needs delivery address
 *
 * @property-read ActiveQuery $log
 * @property-read string $description
 * @property-read string $title
 * @property-read null|string $view
 * @property-read int $amount
 * @property-read Prize $prize
 * @property-read string $itemDescription
 * @property-read string $type
 */
class PrizeItem extends BaseModel implements PrizeInterface
{
    private Prize $item;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->item = $this->getPrize();
        if (!$this->item) {
            throw new Exception(Yii::t('app', 'We are sorry, no prizes left!'));
        }
        $this->item->status = Prize::STATUS_PENDING;
        $this->item->save(false);
    }

    /** @inheritdoc */
    public function getView(): ?string
    {
        return '/prize/prize-item';
    }

    /** @inheritdoc */
    public function getType(): string
    {
        return Yii::t('app', 'item');
    }

    /** @inheritdoc */
    public function getTitle(): string
    {
        return Yii::t('app', $this->item->title);
    }

    /** @inheritdoc */
    public function getDescription(): string
    {
        return Yii::t('app', $this->item->description)
            . PHP_EOL
            . Yii::t('app', 'Fill the form below to get the prize');
    }

    /** @inheritdoc */
    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_ITEM, 'user_id' => Yii::$app->user->id]);
    }

    /** @inheritdoc */
    public function handleReceiving(int $acceptType = null): bool
    {
        $staffNotification = new StaffNotification([
            'message_template' => StaffNotification::TEMPLATE_PRIZE_ITEM
        ]);
        $staffNotification->save(false);
        $this->item->status = Prize::STATUS_AVAILABLE;
        return $this->item->save();
    }

    /** @inheritdoc */
    public function getAmount(): int
    {
        return 1;
    }

    /** @inheritdoc */
    public function hash(): string
    {
        return Yii::$app->security->encryptByKey(json_encode(['itemId' => $this->item->id]), $_ENV['PRIZES_HASH_KEY']);
    }

    /** @inheritdoc */
    public function restoreFromHash(string $hash): void
    {
        $prizeId = json_decode(Yii::$app->security->decryptByKey($hash, $_ENV['PRIZES_HASH_KEY']))['itemId'];
        $this->item = Prize::findOne($prizeId);
    }

    /**
     * Gets random available prize item
     *
     * @return \app\models\Prize|null
     */
    private function getPrize(): ?Prize
    {
        return Prize::find()->where(['status' => Prize::STATUS_AVAILABLE])->orderBy(new Expression('rand()'))->one();
    }
}