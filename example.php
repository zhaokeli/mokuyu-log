<?php
include __dir__ . '/vendor/autoload.php';

$logger = new \ank\Logger([
    'logPath' => __dir__ . '/logs/{Y}{m}/{d}/{type}',
]);
$logger->write('test log', 'sql');
$logger->warning('test log');
// $logger->header('=================');
$logger->save();
