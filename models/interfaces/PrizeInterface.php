<?php

namespace app\models\interfaces;

use yii\db\ActiveQuery;

interface PrizeInterface
{
    /**
     * @return string|null - path of view file
     */
    public function getView(): ?string;

    /**
     * @return int - amount(quantity) of prize
     */
    public function getAmount(): int;

    /**
     * @return string - type of prize
     */
    public function getType(): string;

    /**
     * @return string - title of prize
     */
    public function getTitle(): string;

    /**
     * @return string - description of prize
     */
    public function getDescription(): string;

    /**
     * @return ActiveQuery - query to get logs by current user_id and prize_id
     */
    public function getLog(): ActiveQuery;

    /**
     * @return bool - whether prise receiving was successful or not
     */
    public function handleReceiving(): bool;

    /**
     * @return string - hashed data of generated prize
     */
    public function hash(): string;

    /**
     * @return string - restor object attributes from hash string
     */
    public function restoreFromHash(string $hash): string;
}