<?php

namespace app\services;

use app\models\factory\Prize\PrizeFactory;
use app\models\factory\Prize\type\PrizeMoney;
use app\models\interfaces\PrizeInterface;
use app\models\Prize;
use JetBrains\PhpStorm\ArrayShape;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\Expression;
use yii\web\HttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;

/**
 * Core game class that handle prize creation and perform various checks
 */
class GameService
{
    //region Object Attributes
    #[ArrayShape(['money_pool_amount' => "integer", 'money_pool_amount_reserved' => 'integer'])]
    private array $settings;
    //endregion

    //region General Methods
    /**
     * Need to get settings on each instance creation,
     * so we can operate by actual data stored in settings
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->getSettings();
    }

    /**
     * Get settings from database
     *
     * @throws \yii\base\Exception
     */
    private function getSettings()
    {
        try {
            $this->settings = Yii::$app->db
                ->createCommand(new Expression('SELECT * FROM `settings` WHERE `id` = 1'))
                ->queryOne();
        } catch (Throwable) {
            throw new Exception(Yii::t('app', 'Can\'t get settings from database!'), 500);
        }
    }

    /**
     * @throws NotAcceptableHttpException
     * @throws Exception
     */
    public function getPrize(): PrizeInterface
    {
        if (!$this->canPlay()) {
            throw new NotFoundHttpException(Yii::t('app', 'You already got your prize!'));
        }
        $type = $this->getPrizeType();
        $prize = PrizeFactory::create($type);
        $data = $prize->hash();
        Yii::$app->session->set('currentPrize', $data);
        return $prize;
    }

    /**
     * Checks if there is available item type prize
     *
     * @return bool
     */
    private static function checkAvailablePrizeItems(): bool
    {
        return (bool)Prize::findOne(['status' => Prize::STATUS_AVAILABLE]);
    }

    /**
     * Gets random prize type with checking available prize types
     *
     * @return int - available prize type @see PrizeFactory::TYPE* constants
     * @throws Exception if not available prize type left
     */
    private function getPrizeType(): int
    {
        /** Get all possible prize types */
        $types = array_keys(PrizeFactory::TYPES_CLASS_MAPPING);

        /** Remove item prizes if not any available exist */
        if (!self::checkAvailablePrizeItems()) {
            unset($types[PrizeFactory::TYPE_ITEM]);
        }
        /** Remove money prizes if money pool is empty */
        if (!$this->settings['money_pool_amount']) {
            unset($types[PrizeFactory::TYPE_MONEY]);
        }
        /** get random prize type from rest */
        if (empty($types)) {
            throw new NotFoundHttpException(Yii::t('app', 'There are no prizes left ,comeback later!'));
        }
        return array_rand(array_flip($types));
    }

    /**
     * Reserves money type prize amount in settings table
     *
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function reserveMoney(int $amount): bool
    {
        $currentMoneyPoolAmount = $this->settings['money_pool_amount'];
        if ($amount > $currentMoneyPoolAmount) {
            return false;
        }
        $data = [
            'money_pool_amount' => $currentMoneyPoolAmount - $amount,
            'money_pool_amount_reserved' => $this->settings['money_pool_amount_reserved'] + $amount,
        ];
        return $this->updateSettings($data);
    }

    /**
     * Restore money type prize amount in settings table
     *
     * @throws NotSupportedException
     * @throws \yii\db\Exception
     * @throws \Throwable
     * @throws InvalidConfigException
     */
    public function releaseMoney(int $amount): bool
    {
        $currentMoneyPoolAmount = $this->settings['money_pool_amount'];
        if ($amount < $this->settings['money_pool_amount_reserved']) {
            return false;
        }
        $data = [
            'money_pool_amount' => $currentMoneyPoolAmount + $amount,
            'money_pool_amount_reserved' => $this->settings['money_pool_amount_reserved'] - $amount,
        ];
        return $this->updateSettings($data);
    }

    /**
     * Update settings table in database
     * use transaction to operate with money amounts safely
     *
     * !!!don't use it outside GameService instance or call static!!!
     *
     * @param array $data - array where key is column name and value is value of column
     * @throws InvalidConfigException
     * @throws NotSupportedException
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @internal
     */
    private function updateSettings(array $data): bool
    {
        Yii::$app->db->beginTransaction();
        try {
            Yii::$app->db->createCommand()->update('settings', $data, ['id' => 1])->execute();
            Yii::$app->db->transaction->commit();
        } catch (Throwable $e) {
            Yii::$app->db->transaction->rollBack();
            throw $e;
        }
        return true;
    }
    //endregion

    //region Game Methods
    /**
     * Get the current won prize from session hash
     */
    public static function getCurrentPrize(): ?PrizeInterface
    {
        $currentPrizeHash = Yii::$app->session->get('currentPrize');
        if ($currentPrizeHash) {
            return PrizeFactory::createFromHash($currentPrizeHash);
        }
        return null;
    }

    /**
     * Get the current won prize from session hash and release it from reserve
     */
    public function releaseCurrentPrize(): void
    {
        $prize = $this->getCurrentPrize();
        $prize?->release();
    }

    /**
     * Get the current won prize from session hash and handles it acceptance
     * @throws \yii\web\NotFoundHttpException - if user haven't won a prize yet
     */
    public function acceptCurrentPrize(): PrizeInterface
    {
        /**
         * Get current won prize from hash
         */
        $prize = $this->getCurrentPrize();
        if ($prize) {
            if ($prize instanceof PrizeMoney) {
                $prize->setAcceptType(Yii::$app->request->post('acceptType'));
            }
            $prize->handleAcceptance();
            $this->gameOver();
            return $prize;
        }
        throw new NotFoundHttpException(Yii::t('app', "You haven't won a prize yet!"));
    }

    /**
     * Make further play impossible for current user
     * //TODO: refactor this to more serious later, i.e. database lock flag
     */
    public function gameOver()
    {
        Yii::$app->session->set('prizeAccepted', true);
    }

    /**
     * Checks whether user can play or not
     * @see gameOver
     * @return bool
     */
    public function canPlay(): bool
    {
        return !(bool)Yii::$app->session->get('prizeAccepted');
    }
    //endregion
}