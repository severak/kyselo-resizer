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

route('POST', '/upload-chunked', function (){
    $chunkNumber = $_POST['chunkNumber'];
    $totalChunks = $_POST['totalChunks'];
    $identifier = $_POST['identifier'];
    $filename = $_POST['filename'];

    if ($chunkNumber == $totalChunks && $totalChunks == 1) {
        // easy way - just one big chunk
        $ext = strtolower(pathinfo( $filename, PATHINFO_EXTENSION));
        $sha1 = sha1_file($_FILES['file']['tmp_name']);
        $newFile = '/files/' . $sha1 . '.' . $ext;
        if (move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . $newFile)) {
            /** @var rows $rows */
            $rows = di('rows');
            $exists = $rows->one('files', ['hash'=>$sha1]);
            if ($exists) {
                return jsonResponse(['status'=>'done', 'hash'=>$sha1]);
            }
            $rows->insert('files', ['hash'=>$sha1, 'ext'=>$ext, 'status'=>'uploaded', 'timestamp'=>time()]);
            return jsonResponse(['status'=>'done', 'hash'=>$sha1]);
        }
    } else {
        // hard way - multiple chunks we need to assemble
        if (move_uploaded_file($_FILES['file']['tmp_name'], __DIR__ . '/tmp/' . $identifier . '.chunk' . $chunkNumber)) {
            if ($chunkNumber == $totalChunks) {
                for ($i = 1; $i <= $totalChunks; $i++) {
                    file_put_contents(__DIR__ . '/tmp/' . $identifier . '.all', file_get_contents(__DIR__ . '/tmp/' . $identifier . '.chunk' . $i), FILE_APPEND);
                }
                $ext = strtolower(pathinfo( $filename, PATHINFO_EXTENSION));
                $sha1 = sha1_file(__DIR__ . '/tmp/' . $identifier . '.all');
                rename(__DIR__ . '/tmp/' . $identifier . '.all', __DIR__ . '/files/' . $sha1 . '.' . $ext);
                /** @var rows $rows */
                $rows = di('rows');
                $exists = $rows->one('files', ['hash'=>$sha1]);
                if ($exists) {
                    return jsonResponse(['status'=>'done', 'hash'=>$sha1]);
                }
                $rows->insert('files', ['hash'=>$sha1, 'ext'=>$ext, 'status'=>'uploaded', 'timestamp'=>time()]);
                return jsonResponse(['status'=>'done', 'hash'=>$sha1]);
            } else {
                return jsonResponse(['status'=>'ok, send more']);
            }
        }
    }
    return jsonResponse(['status'=>'something wrong'], 500);
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



