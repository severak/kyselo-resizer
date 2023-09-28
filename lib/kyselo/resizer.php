<?php
namespace kyselo;
class resizer
{
    public $ext;

    function resize($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->ext = $ext;
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                shell_exec('jpegoptim --all-progressive --size=2000 -- ' .escapeshellarg($path));
                break;
            case 'png':
                shell_exec('pngquant --skip-if-larger --quality=65-80 --ext .png --force ' . escapeshellarg($path));
                break;
            default:
                return false;
        }

    }
}
