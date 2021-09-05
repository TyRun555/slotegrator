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
    }

    public function getView(): ?string
    {
        return '/prize/prize-item';
    }

    public function getType(): string
    {
        return Yii::t('app', 'item');
    }

    public function getTitle(): string
    {
        return Yii::t('app', $this->item->title);
    }

    public function getDescription(): string
    {
        return Yii::t('app', $this->item->description)
            . PHP_EOL
            . Yii::t('app', 'Fill the form below to get the prize');
    }

    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_ITEM, 'user_id' => Yii::$app->user->id]);
    }

    public function handleReceiving(int $acceptType = null): bool
    {
        $staffNotification = new StaffNotification([
            'message_template' => StaffNotification::TEMPLATE_PRIZE_ITEM
        ]);
        $staffNotification->save(false);
        $this->item->status = Prize::STATUS_AVAILABLE;
        return $this->item->save();
    }

    public function getAmount(): int
    {
        return 1;
    }

    public function hash(): string
    {
        return Yii::$app->security->encryptByKey(json_encode(['item' => (array)$this->item]), $_ENV['PRIZES_HASH_KEY']);
    }

    /**
     * restore prize data from hash
     *
     * @param string $hash
     * @return string
     */
    public function restoreFromHash(string $hash): string
    {
        $prizePayLoad = json_decode(Yii::$app->security->decryptByKey($hash, $_ENV['PRIZES_HASH_KEY']))['item'];
        $this->item = Prize::findOne($prizePayLoad);
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