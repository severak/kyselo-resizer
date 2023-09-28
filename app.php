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
$dependencies['webroot'] = __DIR__;

function human_readable_bytes($bytes, $decimals = 2, $system = 'binary')
{
    $mod = ($system === 'binary') ? 1024 : 1000;

    $units = array(
        'binary' => array(
            'B',
            'KiB',
            'MiB',
            'GiB',
            'TiB',
            'PiB',
            'EiB',
            'ZiB',
            'YiB',
        ),
        'metric' => array(
            'B',
            'kB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB',
        ),
    );

    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f%s", $bytes / pow($mod, $factor), $units[$system][$factor]);
}

// ROUTY

// HP & LOGIN
route('', '/', function (){
    return render('home');
});

// TODO - chunked uploader with https://github.com/simple-uploader/Uploader

route('GET', '/upload/', function (){
    return render('home');
});

route('POST', '/upload/', function (){
    $originalName = $_FILES['upload']['name'];
    $ext = strtolower(pathinfo( $originalName, PATHINFO_EXTENSION));
    $sha1 = sha1_file($_FILES['upload']['tmp_name']);
    $newFile = '/files/' . $sha1 . '.' . $ext;

    $notAllowed = ['exe'];
    if (in_array($ext, $notAllowed)) {
        flash('This type of file is not allowed!', 'danger');
        return redirect('/');
    }

    if (move_uploaded_file($_FILES['upload']['tmp_name'], __DIR__ . $newFile)) {
        /** @var rows $rows */
        $rows = di('rows');
        $exists = $rows->one('files', ['hash'=>$sha1]);
        if ($exists) {
            return redirect('/upload/' . $sha1);
        }
        $rows->insert('files', ['hash'=>$sha1, 'ext'=>$ext, 'status'=>'uploaded', 'timestamp'=>time()]);
        return redirect('/upload/'. $sha1);
    } else {
        flash('Upload failed.', 'error');
        return redirect('/upload');
    }
    return '';
});

route('GET', '/upload/{hash}', function ($req, $params){
    $hash = $params['hash'];
    /** @var rows $rows */
    $rows = di('rows');
    $file = $rows->one('files', ['hash'=>$hash]);
    if (!$file) {
        flash('File was probably deleted. But you can upload another.', 'danger');
        return response(render('home'), 404);
    }
    return render('status', ['file'=>$file]);
});



