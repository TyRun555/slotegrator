<?php

namespace app\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class BankTransferJob extends BaseObject implements JobInterface
{
    public $data;

    /**
     * @throws \yii\httpclient\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        /** @var \yii\httpclient\Client $client */
        $client = Yii::$app->httpClient;
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://example.com/api/1.0/supplement')
            ->setData($this->data)
            ->send();
        if (!$response->isOk) {
            Yii::warning(
                __METHOD__
                . ": prize transfer failed!"
                . "Transfer data: " . json_encode($this->data) . PHP_EOL
                . "Service response: ". $response->getContent()
            );
        }
    }

}