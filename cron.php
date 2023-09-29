<?php
// see app.php for application logic

if (!file_exists('config.php')) die('APP not configured');

$config = require 'config.php';

// AUTOLOADING

spl_autoload_register(function ($class){
    require __DIR__ . '/lib/' . str_replace('\\', '/', $class) . '.php';
});

$pdo = new PDO('sqlite:' . __DIR__ . '/' . $config['database']);
$rows = new severak\database\rows($pdo);

$toResize = $rows->one('files', ['is_running'=>0, 'status'=>'uploaded']);
if ($toResize) {
    echo 'resizing ' . $toResize['hash'] . '...' . PHP_EOL;
    $rows->update('files', ['hash'=>$toResize['hash'], 'is_running'=>1], ['hash'=>$toResize['hash']]);
    $resizer = new kyselo\resizer();
    if ($resizer->resize(__DIR__ . '/files/' . $toResize['hash'] . '.' . $toResize['ext'])) {
        $rows->update('files', ['hash'=>$toResize['hash'], 'is_running'=>0, 'status'=>'resized', 'ext'=>$resizer->ext], ['hash'=>$toResize['hash']]);
    } else {
        $rows->update('files', ['hash'=>$toResize['hash'], 'is_running'=>0, 'status'=>'error', 'ext'=>$resizer->ext], ['hash'=>$toResize['hash']]);
    }
    echo 'OK ' . $toResize['hash'] . PHP_EOL;
    exit;
}

$beforeHour = date_create('now')->modify('-20 minutes');
$toDelete = $rows->one('files', $rows->fragment('timestamp < ?', [$beforeHour->getTimestamp()]));
if ($toDelete) {
    unlink(__DIR__ . '/files/' . $toDelete['hash'] . '.' . $toDelete['ext']);
    $rows->delete('files', ['hash'=>$toDelete['hash']]);
    echo 'Deleted ' . $toDelete['hash'] . PHP_EOL;
    exit;
}

echo 'OK. Nothing to do.' . PHP_EOL;
