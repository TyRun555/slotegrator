<?php

use Symfony\Component\Dotenv\Dotenv;

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

/**
 * we need it to use .env features, such as keeping passwords out of repo
 */
$dotenv = new Dotenv();
$dotenv->load('./../.env.local');

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
