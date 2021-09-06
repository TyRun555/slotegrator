<?php

namespace app\models\interfaces;

use yii\db\ActiveQuery;

interface PrizeInterface
{
    /**
     * @return string|null - path of view file which renders prize details
     */
    public function getView(): ?string;
    
    /**
     * @return string|null - path of view file which renders prize accept info
     */
    public function getAcceptView(): ?string;

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
     * @return void - restore object attributes from data array specific for each prize type
     */
    public function restore(object $data);
    
    /**
     * Each prize type should be reserved while user won it but not yet accepted
     * if it's not necessary just return true in implementation
     *
     * @return bool - whether reserving was successful
     */
    public function reserve(): bool;
    
    /**
     * Each prize type must be restored when user rejects the won prize
     * if it's not necessary just return true in implementation
     *
     * @return bool - whether reserving was successful
     */
    public function release(): bool;
}