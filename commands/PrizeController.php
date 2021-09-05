<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Prize;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command is provided as actions to operate prizes.
 *
 */
class PrizeController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $title
     * @param string $description
     * @param int $status
     * @return int Exit code
     */
    public function actionAddPrize(string $title, string $description, int $status): int
    {
        $prize = new Prize([
            'title' => $title,
            'description' => $description,
            'status' => $status
        ]);
        if ($prize->save()) {
            echo Yii::t('app', 'Prize (id: {prizeId}) added successfully', ['prizeId' => $prize->id]) . PHP_EOL;
            return ExitCode::OK;
        }
        foreach ($prize->errors as $attribute => $message) {
            echo $attribute . Yii::t('app', ' has error: ') . implode(PHP_EOL, $message) . PHP_EOL;
        }
        return ExitCode::DATAERR;
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $id
     * @param string $attribute
     * @param int $value
     * @return int Exit code
     */
    public function actionChangePrize(string $id, string $attribute, int $value): int
    {
        $prize = new Prize();
        echo $message . "\n";

        return ExitCode::OK;
    }
}
