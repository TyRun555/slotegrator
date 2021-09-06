<?php

use Symfony\Component\Dotenv\Dotenv;

define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require __DIR__ .'/../vendor/autoload.php';

/**
 * we need it to use .env features, such as keeping passwords out of repo
 */
$dotenv = new Dotenv();
$dotenv->load(__DIR__ .'/../.env.test.local');