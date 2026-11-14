<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
$runner = new \Cake\Console\CommandRunner(new \App\Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));
