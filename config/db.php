<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host='.$_ENV['mysql_host'].';dbname='.$_ENV['mysql_db_name'],
    'username' => $_ENV['mysql_username'],
    'password' => $_ENV['mysql_password'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
