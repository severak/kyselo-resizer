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
                $ok = exec('jpegoptim --all-progressive --size=2000 -- ' .escapeshellarg($path));
                break;
            case 'png':
                $ok = exec('pngquant --skip-if-larger --quality=65-80 --ext .png --force ' . escapeshellarg($path));
                break;
            case 'mp4';
                $fakePath = str_replace('.mp4', '.avi', $path);
                rename($path, $fakePath);
                $path = $fakePath;
                $ext = 'avi';
            case '3gp':
            case 'gif':
            case 'm2ts':
            case 'mov':
            case 'avi':
            case 'wmw':
                $newPath = str_replace('.' . $ext, '.mp4', $path);
                // from https://gist.github.com/dvlden/b9d923cb31775f92fa54eb8c39ccd5a9
                $ok = exec('ffmpeg -i '.escapeshellarg($path).' -preset slow -codec:a aac -b:a 128k -codec:v libx264 -pix_fmt yuv420p -b:v 1000k -minrate 500k -maxrate 2000k -bufsize 2000k -vf scale=-2:480 '.escapeshellarg($newPath));
                $this->ext = 'mp4';
                unlink($path);
                if (!file_exists($newPath)) {
                    return false;
                }
                break;
            case 'wav':
            default:
                $this->ext = false;
                return false;
        }
        return $ok!==false;

    }
}
