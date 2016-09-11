<?php

$loader = new Phalcon\Loader;
$loader->registerNamespaces(array(
    'Beehive' => __DIR__ . '/beehive/src',
));
$loader->register();

$app = new Beehive\Kernel\Application;
$app->set('config', function() {
    $config = new \Phalcon\Config([
        'app.aliases' => [
            'Log' => Beehive\Facades\Log::class,
            'App' => Beehive\Facades\App::class,
            'Timer' => Beehive\Facades\Timer::class
        ]
    ]);
    return $config;
});

$app->set('log', function() {
    $multiple = new Phalcon\Logger\Multiple;
    $fileLogger = new Beehive\Logger\Adapter\FileRollSize(__DIR__ . '/../logs/app.log');
    $fileLogger->setFormatter(new Beehive\Logger\Formatter\Line);
    $fileLogger->setLogLevel(Phalcon\Logger::DEBUG);

    $logger = new Beehive\Logger\Adapter\Console;
    $logger->setFormatter(new Beehive\Logger\Formatter\Console);
    $logger->setLogLevel(Phalcon\Logger::DEBUG);

    $multiple->push($logger);
    $multiple->push($fileLogger);
    return $multiple;
});

$app->set('app', $app);
$app->bootstrap();

$app->set('server', function() {
    $server = new Beehive\Server\Registry('0.0.0.0', 6661);
    return $server;
});

$app->set('registry_sortedset', function() {
    $redis = new Redis();
    $redis->connect('192.168.1.234', 6379);
    $storage = new Beehive\Storage\SortedSet\Redis($redis);
    return $storage;
});

App::make('server')->start();