<?php

namespace tests\unit\models;

use app\models\factory\Prize\type\PrizeMoney;
use app\models\User;
use Codeception\Test\Unit;
use ReflectionClass;

class PrizeMoneyTest extends Unit
{
    public function testConvertMoneyToPoints()
    {
        $prize = new PrizeMoney();
        $reflection = new ReflectionClass($prize);
        $convertedToPoints = $reflection->getMethod('convertMoneyToPoints');
        $convertedToPoints->setAccessible(true);
        $moneyAmount = $prize->getAmount();
        $convertedToPointsAmount = $convertedToPoints->invoke($prize);

        expect_that($moneyAmount);
        expect($convertedToPointsAmount)->equals((int)($moneyAmount * $_ENV['MONEY_TO_ACCOUNT_CONVERSION_MULTIPLIER']));
    }
}
