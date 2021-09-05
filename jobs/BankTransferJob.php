<?php

namespace app\jobs;

use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Example money transfer queued job
 * TODO: provide bank account ID of winner to $data attribute and implement real bank API call
 */
class BankTransferJob extends BaseObject implements JobInterface
{
    #[ArrayShape(['bankAccountId' => "string", 'amount' => "int"])]
    public $data;

    /**
     * Transfer money to winner bank account via HTTP request to bank API
     *
     * @throws \yii\httpclient\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue): void
    {
        /** @var \yii\httpclient\Client $client */
        $client = Yii::$app->httpClient;
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://example_bank_api.com/supplement')
            ->setData(['amount' => $this->data['amount'], 'bankAccountId' => 1])
            ->send();
        if (!$response->isOk) {
            Yii::warning(
                __METHOD__
                . ": prize transfer failed!"
                . "Transfer data: " . json_encode($this->data) . PHP_EOL
                . "Service response: ". $response->getContent()
            );
        }
        //TODO: add some logging of successful transfer if needed here
    }

}