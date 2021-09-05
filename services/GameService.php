<?php

namespace app\services;

use app\models\factory\Prize\type\PrizeItem;
use app\models\factory\PrizeFactory;
use app\models\interfaces\PrizeInterface;
use app\models\Prize;
use JetBrains\PhpStorm\Pure;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\web\NotAcceptableHttpException;

class GameService
{
    private array $settings;

    /**
     * @throws \yii\base\Exception
     */
    public function __construct()
    {
        $this->getSettings();
    }

    /**
     * @throws \yii\web\NotAcceptableHttpException
     * @throws \yii\base\Exception
     */
    public function getPrize(): PrizeInterface
    {
        if (Yii::$app->session->get('prizeAccepted')) {
            throw new NotAcceptableHttpException(Yii::t('app', 'You already got your prize!'));
        }
        $type = $this->getType();
        $prize = PrizeFactory::create($type);
        $data = $prize->hash();
        Yii::$app->session->set('currentPrize', $data);
        return $prize;
    }

    private function getSettings()
    {
        try {
            $this->settings = Yii::$app->db
                ->createCommand(new Expression('SELECT * FROM `settings` WHERE `id` = 1'))
                ->queryOne();
        } catch (\Throwable $e) {
            throw new Exception(Yii::t('app', 'Can\'t get settings from database!'), 500);
        }
    }

    private static function checkAvailablePrizeItems(): bool
    {
        return (bool)Prize::findOne(['status' => Prize::STATUS_AVAILABLE]);
    }

    /**
     * get random prize type
     *
     * @return int - available prize type @see PrizeFactory::TYPE* constants
     * @throws \yii\base\Exception if not available prize type left
     */
    private function getType()
    {
        /**
         * Get all possible prize types
         */
        $types = array_keys(PrizeFactory::TYPES);

        /**
         * Remove item prizes if not any available exist
         */
        if (!self::checkAvailablePrizeItems()) {
            unset($types[PrizeFactory::TYPE_ITEM]);
        }
        /**
         * Remove money prizes if money pool is empty
         */
        if (!$this->settings['money_amount_rest']) {
            unset($types[PrizeFactory::TYPE_MONEY]);
        }
        /**
         * get random prize type from rest
         */
        if (empty($types)) {
            throw new Exception(Yii::t('app', 'There are no prizes left ,comeback later!'));
        }
        return array_rand(array_flip($types));
    }
}