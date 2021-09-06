<?php

namespace app\models\factory\Prize;

use app\models\factory\Prize\type\PrizeAccount;
use app\models\factory\Prize\type\PrizeItem;
use app\models\factory\Prize\type\PrizeMoney;
use app\models\interfaces\PrizeInterface;
use Yii;

/**
 * Generates prize object by type
 *
 * @uses $_ENV - provide PRIZES_HASH_KEY in .env.local
 */
class PrizeFactory
{
    /** @var int - random amount money prize */
    const TYPE_MONEY = 1;
    /** @var int - random amount account prize */
    const TYPE_ACCOUNT = 2;
    /** @var int - random amount money prize */
    const TYPE_ITEM = 3;

    const TYPES_CLASS_MAPPING = [
        self::TYPE_MONEY => PrizeMoney::class,
        self::TYPE_ACCOUNT => PrizeAccount::class,
        self::TYPE_ITEM => PrizeItem::class
    ];
    
    /**
     * Create prize by provided type
     *
     * @param int $type - prize type @see self::TYPE_* constants
     * @return PrizeInterface - common prize interface
     */
    public static function create(int $type): PrizeInterface
    {
        return new (self::TYPES_CLASS_MAPPING[$type])();
    }
    
    /**
     * Create prize object from hash string
     * Use it to get current user prize that was won but yet not accepted
     *
     * @param string $hash - encoded string that contains prize data
     */
    public static function createFromHash(string $hash): PrizeInterface
    {
        $restore = true;
        $data = json_decode(Yii::$app->security->decryptByKey($hash, $_ENV['PRIZES_HASH_KEY']));
        
        $prize = new (self::TYPES_CLASS_MAPPING[$data->type])([], $restore);
        $prize->restore($data);
        return $prize;
    }
}