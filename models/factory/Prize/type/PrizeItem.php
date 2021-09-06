<?php

namespace app\models\factory\Prize\type;

use app\models\base\BaseModel;
use app\models\factory\Prize\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\Prize;
use app\models\PrizeLog;
use app\models\StaffNotification;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

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
 * @property null|string $acceptView
 * @property-read string $type
 */
class PrizeItem extends BaseModel implements PrizeInterface
{
    private Prize $item;
    
    /**
     * @param array $config
     * @param bool $restore
     * @throws NotFoundHttpException
     */
    public function __construct($config = [], bool $restore = false)
    {
        parent::__construct($config);
    
        /** We need to do this only if prize was not creating from hash  */
        if (!$restore) {
            $item = $this->getPrize();
            if(!$item)
            {
                throw new NotFoundHttpException(Yii::t('app', 'We are sorry, no prizes left!'));
            }$this->item = $item;
            $this->item->status = Prize::STATUS_PENDING;
            $this->item->save(false);
        }
    }
    
    /** @inheritdoc */
    public function getView(): ?string
    {
        return '/prize/prize-item';
    }
    
    /** @inheritdoc  */
    public function getAcceptView(): ?string
    {
        return '/prize/accept/prize-item';
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
            .PHP_EOL
            .Yii::t('app', 'Fill the form below to get the prize');
    }
    
    /** @inheritdoc */
    public function getLog(): ActiveQuery
    {
        return PrizeLog::find()->where(['prize_type' => PrizeFactory::TYPE_ITEM, 'user_id' => Yii::$app->user->id]);
    }
    
    /** @inheritdoc */
    public function handleAcceptance(int $acceptType = null): bool
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
        $data = json_encode([
            'itemId' => $this->item->id,
            'type'   => PrizeFactory::TYPE_ITEM
        ]);
        return Yii::$app->security->encryptByKey($data, $_ENV['PRIZES_HASH_KEY']);
    }
    
    /** @inheritdoc */
    public function restore(object $data): void
    {
        if(!isset($data->itemId))
        {
            throw new InvalidArgumentException(Yii::t('app', 'Data array must contain amount key!'));
        }
        $this->item = Prize::findOne($data->itemId);
    }
    
    /**
     * Gets random available prize item
     *
     * @return Prize|null
     */
    private function getPrize(): ?Prize
    {
        return Prize::find()->where(['status' => Prize::STATUS_AVAILABLE])->orderBy(new Expression('rand()'))->one();
    }
    
    /** @inheritdoc */
    public function reserve(): bool
    {
        $this->item->status = Prize::STATUS_PENDING;
        return $this->item->save();
    }
    
    /** @inheritdoc */
    public function release(): bool
    {
        $this->item->status = Prize::STATUS_AVAILABLE;
        return $this->item->save();
    }
}