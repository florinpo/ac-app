<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'My Console Application',
    // application components
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=gxc2',
            'schemaCachingDuration' => 3600,
            'emulatePrepare' => true,
            'username' => 'florin',
            'password' => '000000',
            'charset' => 'utf8',
            'tablePrefix' => 'gxc_',
            'enableProfiling' => true,
            'enableParamLogging' => true,
        )
    ),
    'commandMap' => array(
        'migrate' => array(
            'class' => 'system.cli.commands.MigrateCommand',
            //'migrationPath' => 'application.migrations',
            'migrationTable' => 'gxc_migration',
            'connectionID' => 'db',
            //'templateFile' => 'application.migrations.template',
        )
    )
);
