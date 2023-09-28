<?php
// DEPENDENCIES
use severak\database\rows;
use severak\forms\form;

$dependencies['config'] = $config;
$singletons['pdo'] = function() {
    $config = di('config');
    return new PDO('sqlite:' . __DIR__ . '/' . $config['database']);
};
$singletons['rows'] = function(){
    return new severak\database\rows(di('pdo'));
};

// ROUTY

// HP & LOGIN
route('', '/', function (){
    return render('home');
});

