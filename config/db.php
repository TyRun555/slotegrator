<?php

return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DBNAME'],
    'username' => $_ENV['MYSQL_USERNAME'],
    'password' => $_ENV['MYSQL_PASSWORD'],
    'charset'  => 'utf8mb4',
    
    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
