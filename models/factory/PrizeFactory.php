<?php

namespace app\models\factory;

use app\models\factory\Prize\type\PrizeAccount;
use app\models\factory\Prize\type\PrizeItem;
use app\models\factory\Prize\type\PrizeMoney;
use app\models\interfaces\PrizeInterface;

/**
 * Generates prize object by type
 */
class PrizeFactory
{
    /** @var int - random amount money prize */
    const TYPE_MONEY = 1;
    /** @var int - random amount account prize */
    const TYPE_ACCOUNT = 2;
    /** @var int - random amount money prize */
    const TYPE_ITEM = 3;

    const TYPES = [
        self::TYPE_MONEY => PrizeMoney::class,
        self::TYPE_ACCOUNT => PrizeAccount::class,
        self::TYPE_ITEM => PrizeItem::class
    ];

    public static function create(int $type): PrizeInterface
    {
        return new (self::TYPES[$type])();
    }
}