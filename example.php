<?php
include __dir__ . '/vendor/autoload.php';

$logger = new \mokuyu\Log([
    'logPath' => __dir__ . '/logs/{Y}{m}/{d}/{type}',
]);
$logger->write('test log', 'sql');
$logger->warning('test log');
// $logger->header('=================');
$logger->save();
